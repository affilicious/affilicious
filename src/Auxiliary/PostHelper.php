<?php
namespace Affilicious\ProductsPlugin\Auxiliary;

use Affilicious\ProductsPlugin\Product\Product;
use Affilicious\ProductsPlugin\Exception\PostNotFoundException;

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
     * @param int|string|\WP_Post|Product $postOrId
     * @return \WP_Post
     * @throws \Exception
     */
    public static function getPost($postOrId = null)
    {
        if($postOrId instanceof \WP_Post) {
            return $postOrId;
        }

        if($postOrId instanceof Product) {
            return $postOrId->getRawPost();
        }

        if (is_int($postOrId)) {
            $post = get_post($postOrId);
        } elseif (is_string($postOrId)) {
            $post = get_post(intval($postOrId));
        } else {
            $post = get_post();
        }

        if ($post === null) {
            throw new PostNotFoundException($postOrId);
        }

        return $post;
    }
}
