<?php
namespace Affilicious\Product\Presentation\Setup;

use Affilicious\Product\Domain\Model\Product_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Canonical_Setup
{
    /**
     * Set up the canonical link for product variants to avoid duplicated content
     *
     * @since 0.6
     */
    public function set_up()
    {
        $post = get_post();
        if(empty($post) || $post->post_type !== Product_Interface::POST_TYPE || $post->post_parent == 0) {
            return;
        }

        $parent_link = get_post_permalink($post->post_parent);
        if(!empty($parent_link) && !($parent_link instanceof \WP_Error)) {
            echo '<link rel="canonical" href="' . $parent_link . '">';
        }
    }
}
