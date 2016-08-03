<?php
namespace Affilicious\ProductsPlugin\Shop;

use Affilicious\ProductsPlugin\Exception\InvalidPostTypeException;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ShopFactory
{
    /**
     * Create a new shop based on an existing post
     * @param \WP_Post $post
     * @return Shop
     * @throws InvalidPostTypeException
     */
    public function create(\WP_Post $post)
    {
        if($post->post_type !== Shop::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Shop::POST_TYPE);
        }

        $shop = new Shop($post);
        return $shop;
    }
}
