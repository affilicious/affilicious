<?php
namespace Affilicious\Product\Presentation\Setup;

use Affilicious\Product\Domain\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CanonicalSetup
{
    /**
     * Set up the canonical link for product variants to avoid duplicated content
     *
     * @since 0.6
     */
    public function setUp()
    {
        $post = get_post();
        if(empty($post) || $post->post_type !== Product::POST_TYPE || $post->post_parent == 0) {
            return;
        }

        $parentLink = get_post_permalink($post->post_parent);
        if(!empty($parentLink) && !($parentLink instanceof \WP_Error)) {
            echo '<link rel="canonical" href="' . $parentLink . '">';
        }
    }
}
