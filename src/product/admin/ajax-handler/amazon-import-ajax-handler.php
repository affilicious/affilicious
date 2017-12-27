<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Status;
use Affilicious\Product\Import\Import_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Model\Type;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Helper\Shop_Template_Helper;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Import_Ajax_Handler
{
    /**
     * @var Import_Interface
     */
    protected $amazon_import;

    /**
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @var Shop_Template_Factory_Interface
     */
    protected $shop_template_factory;

    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @since 0.9
     * @param Import_Interface $amazon_import
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Factory_Interface $shop_template_factory
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Import_Interface $amazon_import,
        Product_Repository_Interface $product_repository,
        Shop_Template_Factory_Interface $shop_template_factory,
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    ) {
        $this->amazon_import = $amazon_import;
        $this->product_repository = $product_repository;
        $this->shop_template_factory = $shop_template_factory;
        $this->shop_template_repository = $shop_template_repository;
        $this->provider_repository = $provider_repository;
    }

    /**
     * Import the products by making an Amazon provider API call.
     *
     * @hook wp_ajax_aff_product_admin_amazon_import
     * @since 0.9
     */
    public function handle()
    {
    	// Get the post data
		$data = $this->get_data();
	    if($data instanceof \WP_Error) {
		    wp_send_json_error($data, 500);
	    }

        // Create an optional new shop template.
        $shop_template = $this->create_shop_template($data);
        if($shop_template instanceof \WP_Error) {
            wp_send_json_error($shop_template, 500);
        }

        // Import the Amazon product and retry after 3 seconds, if the request has been throttled.
        $product = $this->import($data, $shop_template);
        if($product instanceof \WP_Error) {
            sleep(3);

            $product = $this->import($data, $shop_template);
            if($product instanceof \WP_Error) {
                wp_send_json_error($product, 500);
            }
        }

        // Map the created shop template into an array which can be serialized into JSON.
        if($shop_template !== null) {
            $shop_template = Shop_Template_Helper::to_array($shop_template);

            // Return the created shop template.
            wp_send_json_success([
                'shop_template' => $shop_template
            ]);
        }

        wp_send_json_success();
    }

	/**
	 * Get the unserialized post request data.
	 *
	 * @since 0.9
	 * @return array|\WP_Error
	 */
    protected function get_data()
    {
	    $data = [
		    'product' => !empty($_POST['product']) ? $_POST['product'] : null,
		    'config' => !empty($_POST['config']) ? $_POST['config'] : null,
		    'form' => !empty($_POST['form']) ? $_POST['form'] : null,
	    ];

	    if(empty($data['product']) || empty($data['config']) || empty($data['form'])) {
	    	return new \WP_Error('aff_product_amazon_import_not_valid_request', __('The Amazon import request is not valid.', 'affilicious'));
	    }

	    return $data;
    }

	/**
	 * Create a new shop template with the given name from the POST parameters.
	 *
	 * @since 0.9
	 * @param array $data
	 * @return Shop_Template|null|\WP_Error
	 */
    protected function create_shop_template(array $data)
    {
        $selected_shop = isset($data['config']['selectedShop']) ? $data['config']['selectedShop'] : null;
        if($selected_shop !== 'new-shop') {
            return null;
        }

        $new_shop_name = isset($data['config']['newShopName']) ? $data['config']['newShopName'] : null;
        if(empty($new_shop_name)) {
            return new \WP_Error('aff_product_amazon_import_failed_to_find_new_shop_name', __('Specify a shop name if you want to create a new shop.', 'affilicious'));
        }

        $provider = $this->provider_repository->find_one_by_slug(Amazon_Provider::slug());
        if($provider === null) {
            return new \WP_Error('aff_product_amazon_import_failed_to_find_amazon_provider', __('Failed to find the Amazon provider.', 'affilicious'));
        }

        $shop_template = $this->shop_template_factory->create_from_name(new Name($new_shop_name));
        $shop_template->set_provider_id($provider->get_id());

        $shop_template_id = $this->shop_template_repository->store($shop_template);
        if($shop_template_id instanceof \WP_Error) {
        	if($shop_template_id->get_error_code() == 'term_exists') {
        		return new \WP_Error('aff_product_amazon_import_shop_template_exists', sprintf(
        			__('Failed to import the product because the shop "%s" couldn\'t be created. It\'s already existing.', 'affilicious'),
			        $shop_template->get_name()->get_value()
		        ));
	        }

        	return $shop_template_id;
        }

        return $shop_template;
    }

    /**
     * Import the Amazon product by the POST config and with the given shop template as Amazon shop.
     *
     * @since 0.9
     * @param array $data
     * @param Shop_Template|null $shop_template
     * @return Product|\WP_Error
     */
    protected function import(array $data, Shop_Template $shop_template = null)
    {
        // Extract the search params.
        $asin = isset($data['product']['shops'][0]['tracking']['affiliate_product_id']) ? $data['product']['shops'][0]['tracking']['affiliate_product_id'] : null;
        if(empty($asin)) {
        	$asin = isset($data['product']['custom_values']['amazon_parent_asin']) ? $data['product']['custom_values']['amazon_parent_asin'] : null;

        	if(empty($asin)) {
        		return new \WP_Error('aff_product_amazon_import_failed_to_find_asin', __('Failed to find the ASIN for product.', 'affilicious'));
	        }
        }

        $shop = isset($data['config']['selectedShop']) ? $data['config']['selectedShop'] : null;
        $action = isset($data['config']['selectedAction']) ? $data['config']['selectedAction'] : null;
        $status = isset($data['config']['status']) ? $data['config']['status'] : null;
        $merge_product_id = isset($data['config']['mergeProductId']) ? $data['config']['mergeProductId'] : null;
        $replace_product_id = isset($data['config']['replaceProductId']) ? $data['config']['replaceProductId'] : null;

        // Find the shop template for the import.
	    if($shop_template === null && $shop !== null) {
		    $shop_template = $this->shop_template_repository->find_one_by_slug(new Slug($shop));
	    }

        // Import the product
        $imported_product = $this->amazon_import->import(new Affiliate_Product_Id($asin), [
            'shop_template_id' => $shop_template !== null ? $shop_template->get_id() : null,
	        'variants' => isset($data['product']['type']) && $data['product']['type'] == Type::COMPLEX ? true : false,
        ]);

        // Check for import errors.
        if($imported_product instanceof \WP_Error) {
            return $imported_product;
        }

        // Set the product status.
        if(!empty($status)) {
            $imported_product->set_status(new Status($status));
        }

        // Perform some actions like replacing or merging.
        if($action == 'replace-product' && $replace_product_id !== null) {
            $product = $this->product_repository->find_one_by_id(new Product_Id($replace_product_id));
            if($product !== null) {
                $imported_product = $this->replace_product($imported_product, $product);
            }
        } elseif($action == 'merge-product' && $merge_product_id !== null) {
            $product = $this->product_repository->find_one_by_id(new Product_Id($merge_product_id));
            if($product !== null) {
                $imported_product = $this->merge_product($imported_product, $product);
            }
        }

        // Check for merge or replace errors.
        if($imported_product instanceof \WP_Error) {
            return $imported_product;
        }

        // Store the product as a post.
        $product_id = $this->product_repository->store($imported_product);
        if($product_id instanceof \WP_Error) {
        	return $product_id;
        }

        // TODO: Clean up later (duplicated code)
	    if($imported_product instanceof Complex_Product) {
		    $product_variants = $imported_product->get_variants();
		    foreach ($product_variants as $product_variant) {
			    $this->product_repository->store($product_variant);
		    }

		    $this->product_repository->store($imported_product);
		    $this->product_repository->delete_all_variants_except(
			    $imported_product->get_id(),
			    $product_variants,
			    true
		    );
        }

        return $imported_product;
    }

    /**
     * Merge the imported product with the existing one.
     *
     * @since 0.9
     * @param Product $imported_product
     * @param Product $with_product
     * @return Product|\WP_Error
     */
    protected function merge_product(Product $imported_product, Product $with_product)
    {
        // Check if both product types are compatible to each other.
        if(!$imported_product->get_type()->is_equal_to($with_product->get_type())) {
            return new \WP_Error('aff_product_amazon_import_failed_to_merge_different_product_types', sprintf(
                __('Failed to merge different product types. Got "%s", but the important product is "%s".', 'affilicious'),
                $with_product->get_type()->get_value(),
                $imported_product->get_type()->get_value()
            ));
        }

        $with_product = apply_filters('aff_product_amazon_import_before_merge_products', $with_product, $imported_product);

        // Merge the simple products
        if($imported_product instanceof Simple_Product && $with_product instanceof Simple_Product) {
            $imported_shops = $imported_product->get_shops();
            foreach ($imported_shops as $imported_shop) {
                $with_product->add_shop($imported_shop);
            }

            $imported_images = $imported_product->get_image_gallery();
            $images = $with_product->get_image_gallery();
            $with_product->set_image_gallery(array_merge($imported_images, $images));
        }

        // Merge the complex products
        else if($imported_product instanceof Complex_Product && $with_product instanceof Complex_Product) {
            $imported_variants = $imported_product->get_variants();
            foreach ($imported_variants as $imported_variant) {
                $with_product->add_variant($imported_variant);
            }

            $imported_images = $imported_product->get_image_gallery();
            $images = $with_product->get_image_gallery();
            $with_product->set_image_gallery(array_merge($imported_images, $images));
        }

	    $with_product = apply_filters('aff_product_amazon_import_after_merge_products', $with_product, $imported_product);

        return $with_product;
    }

    /**
     * Replace the imported product with the existing one.
     *
     * @since 0.9
     * @param Product $imported_product
     * @param Product $with_product
     * @return Product
     */
    protected function replace_product(Product $imported_product, Product $with_product)
    {
	    $with_product = apply_filters('aff_product_amazon_import_before_replace_products', $with_product, $imported_product);

	    $temp = $with_product;
        $with_product = clone $imported_product;
        $with_product->set_id($temp->get_id());

	    $with_product = apply_filters('aff_product_amazon_import_after_replace_products', $with_product, $imported_product);

        return $with_product;
    }
}
