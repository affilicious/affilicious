<?php
namespace Affilicious\ProductsPlugin\Auxiliary;

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
     * @param int|string|\WP_Post $post
     * @return \WP_Post
     * @throws \Exception
     */
    public static function getPost($post = null)
    {
        if($post instanceof \WP_Post) {
            return $post;
        }

        if (is_int($post)) {
            $post = get_post($post);
        } elseif (is_string($post)) {
            $post = get_post($post);
        } else {
            $post = get_post();
        }

        if ($post === null) {
            throw new \Exception( __('Failed to find the product group'));
        }

        return $post;
    }
}
