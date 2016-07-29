<?php
namespace Affilicious\ProductsPlugin\Auxiliary;

use Affilicious\ProductsPlugin\Product\Product;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class PostHelper
{
    /**
     * Get the current active post
     * @return array|null|\WP_Post
     */
    public static function getCurrentPost()
    {
        return get_post(get_the_ID());
    }

    /**
     * @param int|string|\WP_Post|Product $post
     * @return \WP_Post
     * @throws \Exception
     */
    public static function getPost($post = null)
    {
        if($post instanceof \WP_Post) {
            return $post;
        }

        if($post instanceof Product) {
            return $post->getRawPost();
        }

        if (is_int($post)) {
            $post = get_post($post);
        } elseif (is_string($post)) {
            $post = get_post(intval($post));
        } else {
            $post = get_post();
        }

        if ($post === null) {
            throw new \Exception( sprintf(
                __('Failed to find the product #%s', 'affiliciousproducts'),
                $post
            ));
        }

        return $post;
    }
}
