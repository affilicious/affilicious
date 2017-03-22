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
     * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id
     * @return bool
     */
    public static function is_product($post_or_id = null)
    {
        // The argument is already a product
        if ($post_or_id instanceof Product) {
            return true;
        }

        // The argument is a product ID
        if($post_or_id instanceof Product_Id) {
            return get_post_type($post_or_id->get_id()) === Product::POST_TYPE;
        }

        // The argument is an integer or string.
        if(is_int($post_or_id) || is_string($post_or_id)) {
            return get_post_type(intval($post_or_id)) === Product::POST_TYPE;
        }

        // The argument is an array
        if(is_array($post_or_id) && !empty($post_or_id['id'])) {
            return get_post(intval($post_or_id['id']));
        }

        // The argument is a post.
        if($post_or_id instanceof \WP_Post) {
            return $post_or_id->post_type === Product::POST_TYPE;
        }

        // The argument is empty. Use the current post.
        if($post_or_id === null) {
            return get_post_type() === Product::POST_TYPE;
        }

        return false;
    }

    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.6
     * @param int|string|array|\WP_Post|Product|Product_Id|null $post_or_id
     * @return null|Product
     */
    public static function get_product($post_or_id = null)
    {
        $product_repository = \Affilicious_Plugin::get('affilicious.product.repository.product');

        // The argument is already a product.
        if ($post_or_id instanceof Product) {
            return $post_or_id;
        }

        // The argument is a product ID
        if($post_or_id instanceof Product_Id) {
            return $product_repository->find_one_by_id($post_or_id);
        }

        // The argument is an integer or string.
        if(is_int($post_or_id) || is_string($post_or_id)) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id));
        }

        // The argument is an array,
        if(is_array($post_or_id) && !empty($post_or_id['id'])) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id['id']));
        }

        // The argument is a post.
        if($post_or_id instanceof \WP_Post) {
            return $product_repository->find_one_by_id(new Product_Id($post_or_id->ID));
        }

        // The argument is null. Use the current post.
        if($post_or_id === null) {
            $post = get_post($post_or_id);
            return $post !== null ?$product_repository->find_one_by_id(new Product_Id($post->ID)) : null;
        }

        return null;
    }
}
