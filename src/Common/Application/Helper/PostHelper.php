<?php
namespace Affilicious\Common\Application\Helper;

use Affilicious\Product\Domain\Exception\PostNotFoundException;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class PostHelper
{
    /**
     * Get the Wordpress post from the ID, Product, DetailGroup and Shop
     *
     * @since 0.3
     * @param mixed $postOrId
     * @return \WP_Post
     * @throws PostNotFoundException
     */
    public static function getPost($postOrId = null)
    {
        if($postOrId instanceof \WP_Post) {
            return $postOrId;
        }

        if(method_exists($postOrId, 'getRawPost')) {
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
