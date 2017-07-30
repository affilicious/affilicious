<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Search\Search_Interface;

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
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @since 0.9
     * @param Search_Interface $amazon_search
     * @param Product_Repository_Interface $product_repository
     */
    public function __construct(Search_Interface $amazon_search, Product_Repository_Interface $product_repository)
    {
        $this->amazon_search = $amazon_search;
        $this->product_repository = $product_repository;
    }

    /**
     * Search the products by making an Amazon provider API call.
     *
     * @hook wp_ajax_aff_product_admin_amazon_search
     * @since 0.9
     */
    public function handle()
    {
        // Search for the Amazon products based on the GET parameters.
        $products = $this->search();
        if($products instanceof \WP_Error) {
            wp_send_json_error($products, 500);
        }

        $products = apply_filters('aff_product_admin_ajax_handler_amazon_search_handle', $products);

        // Map the products into arrays which can be serialized into JSON.
        $products = array_map(function(Product $product) {
            $array = Product_Helper::to_array($product);

            return $array;
        }, $products);

        $products = apply_filters('aff_product_admin_ajax_handler_amazon_search_formatted_handle', $products);

        // Return the json response.
        wp_send_json_success($products);
    }

    /**
     * Search for the Amazon products based on the GET parameters.
     *
     * @since 0.9
     * @return Product[]|\WP_Error
     */
    protected function search()
    {
        // Extract the search params.
        $term = !empty($_GET['term']) ? $_GET['term'] : null;
        $type = !empty($_GET['type']) ? $_GET['type'] : null;
        $category = !empty($_GET['category']) ? $_GET['category'] : null;
        $with_variants = !empty($_GET['with-variants']) && $_GET['with-variants'] == 'yes';
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;

        // Perform the Amazon search.
        $products = $this->amazon_search->search([
            'term' => $term,
            'type' => $type,
            'category' => $category,
            'with_variants' => $with_variants,
            'page' => $page
        ]);

        return $products;
    }

    protected function add_extra_data(Product $product, $array)
    {
    	if($product instanceof Complex_Product) {
		    $array['extra']['complex']['affiliate_product_id'] = null;
	    }

	    return $array;
    }
}
