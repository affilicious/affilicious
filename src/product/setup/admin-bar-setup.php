<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Admin_Bar_Setup
{
    /**
     * Set up the correct product edit link for product variants
     *
     * @hook admin_bar_menu
     * @since 0.6
     * @param \WP_Admin_Bar $wp_admin_bar
     */
    public function set_up(\WP_Admin_Bar $wp_admin_bar)
    {
        $edit_node = $wp_admin_bar->get_node('edit');
        if(empty($edit_node)) {
            return;
        }

        $post = get_post();
        if(empty($post) || $post->post_type !== Product::POST_TYPE || $post->post_parent == 0) {
            return;
        }

        $edit_node->href = preg_replace('/post=(\d+)/', 'post=' . $post->post_parent, $edit_node->href);
        $wp_admin_bar->add_node($edit_node);
    }
}
