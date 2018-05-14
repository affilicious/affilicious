<?php
namespace Affilicious\Common\Table_Creator;

use Affilicious\Common\Helper\Network_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.19
 */
class Logs_Table_Creator
{
	/**
	 * @since 0.9.19
	 * @var string
	 */
	const TABLE_NAME = 'aff_logs';

	/**
	 * Get the full table name of the logs table with prefix.
	 *
	 * @since 0.9.19
	 * @param bool $with_prefix Whether to use the Wordpress table prefix or not.
	 * @param null|int $blog_id The ID of the blog to get the table from.
	 * @return string The table name for the logs table.
	 */
	public static function get_table_name($with_prefix = true, $blog_id = null)
	{
		if($blog_id === null) {
			$blog_id = get_current_blog_id();
		}

		$table_name = null;

		Network_Helper::for_blog($blog_id, function() use ($with_prefix, &$table_name) {
			global $wpdb;

			$table_name = $with_prefix ? $wpdb->prefix : '';
			$table_name .= self::TABLE_NAME;
		});

		return $table_name;
	}

	/**
	 * Create the logs table for the current site.
	 *
	 * @since 0.9.19
	 */
	public function create()
	{
		global $wpdb;

		// Find the charset the build the table name
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = self::get_table_name();

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$sql = "CREATE TABLE {$table_name} (id mediumint(9) NOT NULL AUTO_INCREMENT, `level` varchar(255) NOT NULL, `message` text NOT NULL, `context` varchar(255) NOT NULL, `created_at` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, PRIMARY KEY (id)) {$charset_collate};";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
}
