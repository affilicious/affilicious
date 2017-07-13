<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Model\Product;
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
     * @since 0.9
     * @param Search_Interface $amazon_search
     */
    public function __construct(Search_Interface $amazon_search)
    {
        $this->amazon_search = $amazon_search;
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
}
