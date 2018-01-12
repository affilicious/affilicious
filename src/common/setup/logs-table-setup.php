<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Logs_Table_Setup
{
    const TABLE_NAME = 'aff_logs';

    /**
     * Get the full table name of the logs table with prefix.
     *
     * @since 0.9.18
     * @param bool $with_prefix Whether to use the Wordpress table prefix or not.
     * @return string The table name for the logs table.
     */
    public static function get_table_name($with_prefix = true)
    {
        global $wpdb;

        $table_name = $with_prefix ? $wpdb->prefix : '';
        $table_name .= self::TABLE_NAME;

        return $table_name;
    }

    /**
     * Initialize the logs table used to store the produced logs.
     *
     * @since 0.9.18
     */
    public function init()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        // Find the charset the build the table name
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = self::get_table_name();

        $sql = "CREATE TABLE {$table_name} (id mediumint(9) NOT NULL AUTO_INCREMENT, `level` varchar(255) default NULL, `message` text default NULL, `context` varchar(255) default NULL, `created_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, UNIQUE KEY id (id)) {$charset_collate};";
        dbDelta($sql);
    }
}
