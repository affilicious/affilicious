<?php
namespace Affilicious\Product\Migration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8.12
 */
final class Product_Slug_Migration
{
    /**
     * Migrate the old slug from "product" to "products" to make it more rest conform.
     * The old slug has to be persisted into the database to avoid 404 error sites.
     *
     * @since 0.8.12
     */
    public function migrate()
    {
        if(get_option('_affilicious_migrated_product_slug') == 'yes') {
            return;
        }

        $slug = carbon_get_theme_option('affilicious_options_product_container_general_tab_slug_field');
        if(empty($slug)) {
            if(update_option('affilicious_options_product_container_general_tab_slug_field', 'product')) {
                add_option('affilicious_options_product_container_general_tab_slug_field', 'product');
            }
        }

        if(update_option('_affilicious_migrated_product_slug', 'yes')) {
            add_option('_affilicious_migrated_product_slug', 'yes', null, false);
        }
    }
}
