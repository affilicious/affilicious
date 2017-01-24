<?php
namespace Affilicious\Product\Helper;

use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Helper
{
    /**
     * Check if the ID or Wordpress post belongs to a product.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.7.1
     * @param int|\WP_Post|Product|null $product_or_id
     * @return bool
     */
    public static function is_product($product_or_id = null)
    {
        // The argument is already a product
        if ($product_or_id instanceof Product) {
            return true;
        }

        // The argument is an integer.
        if(is_int($product_or_id)) {
            $post_type = get_post_type($product_or_id);

            return $post_type === Product::POST_TYPE;
        }

        // The argument is a post.
        if($product_or_id instanceof \WP_Post) {
            $post_type = $product_or_id->post_type;

            return $post_type === Product::POST_TYPE;
        }

        // The argument is empty.
        if($product_or_id === null) {
            $post_type =  get_post_type();

            return $post_type === Product::POST_TYPE;
        }

        return false;
    }

    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|\WP_Post|Product|null $product_or_id
     * @return null|Product
     */
    public static function get_product($product_or_id = null)
    {
        $container = \Affilicious_Plugin::get_instance()->get_container();
        $product_repository = $container['affilicious.product.repository.product'];
        $product = null;

        // The argument is already a product.
        if ($product_or_id instanceof Product) {
            $product = $product_or_id;
        }

        // The argument is an integer.
        if(is_int($product_or_id)) {
            $product = $product_repository->find_by_id(new Product_Id($product_or_id));
        }

        // The argument is a post.
        if($product_or_id instanceof \WP_Post) {
            $product = $product_repository->find_by_id(new Product_Id($product_or_id->ID));
        }

        // The argument is empty.
        if($product_or_id === null) {
            $post = get_post($product_or_id);
            if ($post === null) {
                return null;
            }

            $product = $product_repository->find_by_id(new Product_Id($post->ID));
        }

        return $product;
    }
}
