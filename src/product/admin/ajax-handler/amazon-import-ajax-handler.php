<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Status;
use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Import\Import_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
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
     * @since 0.9
     * @param Import_Interface $amazon_import
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Factory_Interface $shop_template_factory
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(
        Import_Interface $amazon_import,
        Product_Repository_Interface $product_repository,
        Shop_Template_Factory_Interface $shop_template_factory,
        Shop_Template_Repository_Interface $shop_template_repository
    ) {
        $this->amazon_import = $amazon_import;
        $this->product_repository = $product_repository;
        $this->shop_template_factory = $shop_template_factory;
        $this->shop_template_repository = $shop_template_repository;
    }

    /**
     * Import the products by making an Amazon provider API call.
     *
     * @hook wp_ajax_aff_product_admin_amazon_import
     * @since 0.9
     */
    public function handle()
    {
        // Create an optional new shop template.
        $shop_template = $this->create_shop_template();
        if($shop_template instanceof \WP_Error) {
            wp_send_json_error($shop_template, 500);
        }

        // Import the Amazon product
        $product = $this->import($shop_template);
        if($product instanceof \WP_Error) {
            wp_send_json_error($product, 500);
        }

        $product = apply_filters('aff_product_admin_ajax_handler_amazon_import_handle', $product, $shop_template);

        // Map the imported product into an array which can be serialized into JSON.
        $product = Product_Helper::to_array($product);
        $product = apply_filters('aff_product_admin_ajax_handler_amazon_import_formatted_handle', $product, $shop_template);

        // Return the json response.
        wp_send_json_success($product);
    }

    /**
     * Create a new shop template with the given name from the POST parameters.
     *
     * @since 0.9
     * @return null|Shop_Template|\WP_Error
     */
    protected function create_shop_template()
    {
        $selected_shop = isset($_POST['config']['selectedShop']) ? $_POST['config']['selectedShop'] : null;
        if($selected_shop !== 'new-shop') {
            return null;
        }

        $new_shop_name = isset($_POST['config']['newShopName']) ? $_POST['config']['newShopName'] : null;
        if(empty($new_shop_name)) {
            return new \WP_Error('aff_product_amazon_import_failed_to_find_new_shop_name', 'Specify a shop name if you want to create a new shop.');
        }

        $shop_template = $this->shop_template_factory->create_from_name(new Name($new_shop_name));
        $this->shop_template_repository->store($shop_template);

        return $shop_template;
    }

    /**
     * Import the Amazon product by the POST config and with the given shop template as Amazon shop.
     *
     * @since 0.9
     * @param Shop_Template|null $shop_template
     * @return Product|\WP_Error
     */
    protected function import(Shop_Template $shop_template = null)
    {
        // Extract the search params.
        $asin = isset($_POST['product']['shops'][0]['tracking']['affiliate_product_id']) ? $_POST['product']['shops'][0]['tracking']['affiliate_product_id'] : null;
        $action = isset($_POST['config']['selectedAction']) ? $_POST['config']['selectedAction'] : null;
        $status = isset($_POST['config']['status']) ? $_POST['config']['status'] : null;
        $merge_product_id = isset($_POST['config']['mergeProductId']) ? $_POST['config']['mergeProductId'] : null;
        $replace_product_id = isset($_POST['config']['replaceProductId']) ? $_POST['config']['replaceProductId'] : null;

        // Import the product
        $imported_product = $this->amazon_import->import(new Affiliate_Product_Id($asin), [
            'shop_template_id' => $shop_template !== null ? $shop_template->get_id() : null
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
        $this->product_repository->store($imported_product);

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
                'Failed to merge different product types. Got "%s", but the important product is "%s".',
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
        $temp = $with_product;
        $with_product = clone $imported_product;
        $with_product->set_id($temp->get_id());

        return $with_product;
    }
}
