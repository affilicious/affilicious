<?php
namespace Affilicious\Product\Application\Helper;

use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Product_Id;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Helper
{
    /**
     * Get the product by the ID or Wordpress post.
     * If you pass in nothing as a parameter, the current post will be used.
     *
     * @since 0.3
     * @param int|\WP_Post|Product|null $product_or_id
     * @return null|Product
     */
    public static function get_product($product_or_id = null)
    {
        $container = \Affilicious_Plugin::get_instance()->get_container();
        $product_repository = $container['affilicious.product.infrastructure.repository.product'];
        $product = null;

        // the argument is already a product or a product variant
        if ($product_or_id instanceof Product) {
            $product = $product_or_id;
        }

        // The argument is an integer
        if(is_int($product_or_id)) {
            $product = $product_repository->find_by_id(new Product_Id($product_or_id));
        }

        // The argument is a post
        if($product_or_id instanceof \WP_Post) {
            $product = $product_repository->find_by_id(new Product_Id($product_or_id->ID));
        }

        // The argument is empty
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
