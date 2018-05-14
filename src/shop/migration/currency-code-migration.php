<?php
namespace Affilicious\Shop\Migration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
class Currency_Code_Migration
{
    /**
     * Migrate the old currency codes to the new ones.
     *
     * @since 0.7
     */
    public function migrate()
    {
        global $wpdb;

        $wpdb->query("
            UPDATE $wpdb->postmeta postmeta
            SET postmeta.meta_value = 'EUR'
            WHERE postmeta.meta_key LIKE '_affilicious_%_currency_%'
            AND postmeta.meta_value = 'euro'
        ");

        $wpdb->query("
            UPDATE $wpdb->postmeta postmeta
            SET postmeta.meta_value = 'USD'
            WHERE postmeta.meta_key LIKE '_affilicious_%_currency_%'
            AND postmeta.meta_value = 'us-dollar'
        ");
    }
}
