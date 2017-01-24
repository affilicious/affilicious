<?php
namespace Affilicious\Common\Helper;


use Affilicious\Common\Exception\Post_Not_Found_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Post_Helper
{
    /**
     * Get the Wordpress post from the ID, Product, detail template group,
     * attribute template group and shop template
     *
     * @since 0.6
     * @param mixed $post_or_id
     * @return \WP_Post
     * @throws Post_Not_Found_Exception
     */
    public static function get_post($post_or_id = null)
    {
        if($post_or_id instanceof \WP_Post) {
            return $post_or_id;
        }

        if(method_exists($post_or_id, 'get_raw_post')) {
            return $post_or_id->get_raw_post();
        }

        if (is_int($post_or_id)) {
            $post = get_post($post_or_id);
        } elseif (is_string($post_or_id)) {
            $post = get_post(intval($post_or_id));
        } else {
            $post = get_post();
        }

        if ($post === null) {
            throw new Post_Not_Found_Exception($post_or_id);
        }

        return $post;
    }
}
