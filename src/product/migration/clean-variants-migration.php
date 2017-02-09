<?php
namespace Affilicious\Product\Migration;

use Affilicious\Product\Model\Product;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Clean_Variants_Migration
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

                $parent_post = get_post($parent_id);
                if(!empty($parent_post)) {
                    continue;
                }

                wp_delete_post($query->post->ID);
            }

            wp_reset_postdata();
        }
    }
}
