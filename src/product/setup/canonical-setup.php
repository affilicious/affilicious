<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.1
 */
class Canonical_Setup
{
    /**
     * Set up the canonical link for product variants to avoid duplicated content
     *
     * @hook wp_head
     * @since 0.9.1
     */
    public function init()
    {
        $post = get_post();
        if(empty($post) || !($post instanceof \WP_Post) || !is_single($post) || $post->post_type !== Product::POST_TYPE || $post->post_parent == 0) {
            return;
        }

        $parent_link = get_post_permalink($post->post_parent);
        if(!empty($parent_link) && !($parent_link instanceof \WP_Error)) {
            echo '<link rel="canonical" href="' . $parent_link . '">';
        }
    }
}
