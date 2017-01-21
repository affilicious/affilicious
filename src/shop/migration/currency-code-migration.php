<?php
namespace Affilicious\Shop\Migration;

use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

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
