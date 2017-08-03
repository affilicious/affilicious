<?php
namespace Affilicious\Product\Migration;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Variant_Inherit_Status_Migration
{
    /**
     * Delete the old variants without a valid parent product.
     *
     * @since 0.8.4
     */
    public function migrate()
    {
        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'posts_per_page' => -1,
        ));

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $parent_id = wp_get_post_parent_id($query->post->ID);
                if($parent_id === false || $parent_id == 0) {
                    continue;
                }

                wp_update_post(array(
                    'ID' => $query->post->ID,
                    'post_status' => 'inherit'
                ));
            }

            wp_reset_postdata();
        }
    }
}
