<?php
namespace Affilicious\Product\Migration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Affiliate_Product_Id_To_090_Migration
{
    const OPTION = 'aff_migrated_product_affiliate_product_id_to_0.9.0';

    /**
     * Rename the "affiliate_id" to "affiliate_product_id".
     *
     * @since 0.9
     */
    public function migrate()
    {
        global $wpdb;

        if(\Affilicious::VERSION >= '0.9' && get_option(self::OPTION) != 'yes') {
            $wpdb->query("
                UPDATE $wpdb->postmeta
                SET meta_key = REPLACE(meta_key, 'affiliate_id', 'affiliate_product_id')
                WHERE meta_key LIKE '_affilicious_%_affiliate_id%'
            ");

            update_option(self::OPTION, 'yes');
       }
    }
}
