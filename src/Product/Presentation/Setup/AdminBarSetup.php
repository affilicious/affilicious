<?php
namespace Affilicious\Product\Presentation\Setup;

use Affilicious\Product\Domain\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AdminBarSetup
{
    /**
     * Set up the correct product edit link for product variants
     *
     * @since 0.6
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function setUp(\WP_Admin_Bar $wpAdminBar)
    {
        $editNode = $wpAdminBar->get_node('edit');
        if(empty($editNode)) {
            return;
        }

        $post = get_post();
        if(empty($post) || $post->post_type !== Product::POST_TYPE || $post->post_parent == 0) {
            return;
        }

        $editNode->href = preg_replace('/post=(\d+)/', 'post=' . $post->post_parent, $editNode->href);
        $wpAdminBar->add_node($editNode);
    }
}
