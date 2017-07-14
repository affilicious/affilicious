<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Common\Model\Name;
use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Import\Import_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Search\Search_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Search_Ajax_Handler
{
    /**
     * @var Search_Interface
     */
    protected $amazon_search;

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
     * @param Search_Interface $amazon_search
     * @param Import_Interface $amazon_import
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Factory_Interface $shop_template_factory
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(
        Search_Interface $amazon_search,
        Import_Interface $amazon_import,
        Product_Repository_Interface $product_repository,
        Shop_Template_Factory_Interface $shop_template_factory,
        Shop_Template_Repository_Interface $shop_template_repository
    ) {
        $this->amazon_search = $amazon_search;
        $this->amazon_import = $amazon_import;
        $this->product_repository = $product_repository;
        $this->shop_template_factory = $shop_template_factory;
        $this->shop_template_repository = $shop_template_repository;
    }

    /**
     * Search the products by making an Amazon provider API call.
     *
     * @hook wp_ajax_aff_product_admin_amazon_search
     * @since 0.9
     */
    public function search()
    {
        // Extract the search params.
        $term = isset($_GET['term']) ? $_GET['term'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $category = isset($_GET['category']) ? $_GET['category'] : null;

        // Perform the Amazon search.
        $products = $this->amazon_search->search([
            'term' => $term,
            'type' => $type,
            'category' => $category,
        ]);

        // Check for search errors.
        if($products instanceof \WP_Error) {
            status_header(400);
            wp_die($products->get_error_message());
        }

        // Map the products into arrays, which can be serialized.
        $products = array_map(function(Product $product) {
            return Product_Helper::to_array($product);
        }, $products);

        // Return the json response.
        $result = json_encode($products);

        status_header(200);
        die($result);
    }

    /**
     * Import the products by making an Amazon provider API call.
     *
     * @hook wp_ajax_aff_product_admin_amazon_import
     * @since 0.9
     */
    public function import()
    {
        $asin = isset($_GET['aff-asin']) ? $_GET['aff-asin'] : null;
        $shop = isset($_GET['aff-shop']) ? $_GET['aff-shop'] : null;
        $new_shop_name = isset($_GET['aff-new-shop-name']) ? $_GET['aff-new-shop-name'] : null;
        $action = isset($_GET['aff-action']) ? $_GET['aff-action'] : null;
        $merge_product_id = isset($_GET['aff-merge-product-id']) ? $_GET['aff-merge-product-id'] : null;
        $replace_product_id = isset($_GET['aff-replace-product-id']) ? $_GET['aff-replace-product-id'] : null;

        if($asin === null) {
            status_header(400);
            wp_die('The Amazon ASIN is missing.');
        }

        if($shop === null) {
            status_header(400);
            wp_die('The shop is missing.');
        }

        if($action === null) {
            status_header(400);
            wp_die('The action is missing.');
        }

        $config = [];

        if($shop == 'new-shop') {
            $shop_template = $this->create_shop_template(new Name($new_shop_name));
            $config['shop_template_id'] = $shop_template->get_id();
        }

        $imported_product = $this->amazon_import->import(new Affiliate_Product_Id($asin), $config);

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

        $this->product_repository->store($imported_product);

        wp_die();
    }

    /**
     * Create a new shop template with the given name.
     *
     * @since 0.9
     * @param Name $name
     * @return Shop_Template
     */
    private function create_shop_template(Name $name)
    {
        $shop_template = $this->shop_template_factory->create_from_name($name);
        $this->shop_template_repository->store($shop_template);

        return $shop_template;
    }

    /**
     * Merge the imported product with the existing one.
     *
     * @since 0.9
     * @param Product $imported_product
     * @param Product $with_product
     * @return Product|\WP_Error
     */
    private function merge_product(Product $imported_product, Product $with_product)
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

        // Marge products of custom type
        else {
            $with_product = apply_filters('aff_product_amazon_import_after_merge_products', $with_product, $imported_product);
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
    private function replace_product(Product $imported_product, Product $with_product)
    {
        $with_product = apply_filters('aff_product_amazon_import_before_replace_products', $with_product, $imported_product);

        $temp = $with_product;
        $with_product = clone $imported_product;
        $with_product->set_id($temp->get_id());

        $with_product = apply_filters('aff_product_amazon_import_after_replace_products', $with_product, $imported_product);

        return $with_product;
    }
}
