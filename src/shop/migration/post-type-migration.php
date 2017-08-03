<?php
namespace Affilicious\Shop\Migration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Post_Type_Migration
{
    /**
     * Migrate the old shop template post type from "shop" to "aff_shop_template"
     * to prevent any unnecessary collisions with other plugins.
     *
     * @since 0.7
     */
    public function migrate()
    {
        global $wpdb;

        $wpdb->query("
            UPDATE $wpdb->posts posts
            SET posts.post_type = 'aff_shop_template'
            WHERE posts.post_type = 'shop'
        ");
    }
}
