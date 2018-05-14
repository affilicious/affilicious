<?php
namespace Affilicious\Common\Admin\System;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Table_Creator\Logs_Table_Creator;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Update\Update_Semaphore;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.9
 */
class System_Info
{
	/**
	 * Generate the system info to make the support easier.
	 *
	 * @note Inspired by Easy Digital Downloads system-info.php
	 * @since 0.9.9
	 * @return array
	 */
	public function generate()
	{
		$info = [
			'Affilicious' => $this->get_affilicious_section(),
			'Wordpress' => $this->get_wordpress_section(),
			'PHP and MySQL' => $this->get_php_and_mysql_section(),
			'Active plugins' => $this->get_active_plugins_section(),
			'Network Active Plugins' => $this->get_network_active_plugins_section(),
		];

		foreach ($info as $section => $options) {
			if(empty($options)) {
				unset($info[$section]);
			}
		}

		$info = apply_filters('aff_common_admin_system_info_generate', $info);
		Assert_Helper::is_array($info, __METHOD__, 'Expected the system info to be an array. Got: %s', '0.9.9');

		return $info;
	}

	/**
	 * Stringify the system info to make the support easier.
	 *
	 * @since 0.9.9
	 * @param bool $nl2br Convert the new line into <br>.
	 * @return string
	 */
	public function stringify($nl2br = false)
	{
		$info = $this->generate();
		$result = '';

		foreach ($info as $section => $options) {
			$result .= "========== {$section} ==========\n";

			foreach ($options as $name => $value) {
				$result .= "{$name}: {$value}\n";
			}

			$result .= "\n";
		}

		if($nl2br) {
			$result = nl2br($result);
		}

		$result = apply_filters('aff_common_admin_system_info_stringify', $result);
		Assert_Helper::is_string_not_empty($result, __METHOD__, 'Expected the system info to be a string. Got: %s', '0.9.9');

		return $result;
	}

    /**
     * Render the system info to make the support easier.
     *
     * @since 0.9.9
     * @param bool $escape Whether to escape the output or not.
     */
	public function render($escape = true)
	{
		$info = $this->stringify(true);

		$info = apply_filters('aff_common_admin_system_info_render', $info);
		Assert_Helper::is_string_not_empty($info, __METHOD__, 'Expected the system info to be a string. Got: %s', '0.9.9');

        if($escape) {
            $info = esc_html($info);
        }

        echo $info;
	}

	/**
	 * Get the Affilicious section for the system info.
	 *
	 * @since 0.9.9
	 * @return array
	 */
	protected function get_affilicious_section()
	{
		$section = [
			'Affilicious Version' => \Affilicious::VERSION,
			'Product Slug' => $this->get_product_slug(),
			'Product Count' => $this->count_all_products(),
			'Trashed Product Count' => $this->count_all_trashed_products(),
			'Orphaned Product Variant Count' => $this->count_orphaned_product_variants(),
			'Update Semaphore Counter (Hourly)' => $this->get_update_semaphore_counter('hourly'),
			'Update Semaphore Last Acquire Time (Hourly)' => $this->get_update_semaphore_last_acquire_time('hourly', true),
			'Update Semaphore Last Acquire Time (Hourly) (GMT)' => $this->get_update_semaphore_last_acquire_time('hourly'),
			'Update Semaphore Counter (Twice Daily)' => $this->get_update_semaphore_counter('twicedaily'),
			'Update Semaphore Last Acquire Time (Twice Daily)' => $this->get_update_semaphore_last_acquire_time('twicedaily', true),
			'Update Semaphore Last Acquire Time (Twice Daily) (GMT)' => $this->get_update_semaphore_last_acquire_time('twicedaily'),
			'Update Semaphore Counter (Daily)' => $this->get_update_semaphore_counter('daily'),
			'Update Semaphore Last Acquire Time (Daily)' => $this->get_update_semaphore_last_acquire_time('daily', true),
			'Update Semaphore Last Acquire Time (Daily) (GMT)' => $this->get_update_semaphore_last_acquire_time('daily'),
			'Log Table Installed' => $this->is_log_table_installed() ? 'Yes' : 'No',
			'Log Table Records Count' => $this->count_log_table_records(),
		];

		$section = apply_filters('aff_common_admin_system_info_generate_affilicious', $section);
		Assert_Helper::is_array($section, __METHOD__, 'Expected the "Affilicious" section to be an array. Got: %s', '0.9.9');

		return $section;
	}

    /**
     * Get the Wordpress section for the system info.
     *
     * @since 0.9.9
     * @return array
     */
    protected function get_wordpress_section()
    {
        global $wpdb;

        $section = [
            'Multisite' => is_multisite() ? 'Yes' : 'No',
            'Site URL' => site_url(),
            'Home URL' => home_url(),
            'Wordpress Version' => get_bloginfo('version'),
            'Permalink Structure' => get_option('permalink_structure'),
            'Active Theme' =>  wp_get_theme(),
            'WP_DEBUG' => defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set',
            'WP Table Prefix' => 'Length: '. strlen($wpdb->prefix) . ' Status: ' . (strlen($wpdb->prefix ) > 16 ? 'ERROR: Too Long' : 'Acceptable'),
            'Show On Front' => get_option('show_on_front'),
            'Page For Front' => get_the_title(get_option('page_on_front')),
            'Page For Blog' => get_the_title(get_option('page_for_posts')),
        ];

        $section = apply_filters('aff_common_admin_system_info_generate_wordpress', $section);
        Assert_Helper::is_array($section, __METHOD__, 'Expected the "Wordpress" section to be an array. Got: %s', '0.9.9');

        return $section;
    }

    /**
     * Get the PHP and MySQL section for the system info.
     *
     * @since 0.9.9
     * @return array
     */
    protected function get_php_and_mysql_section()
    {
        global $wpdb;

        $section = [
            'PHP Version' => PHP_VERSION,
            'MySQL Version' => $wpdb->db_version(),
            'Web Server Info' => !empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '-',
            'Time' => current_time('mysql', 0),
            'Time (GMT)' => current_time('mysql', 1),
            'PHP Safe Mode' => ini_get('safe_mode') ? 'Yes' : 'No',
            'PHP Memory Limit' => ini_get('memory_limit'),
            'PHP Upload Max Size' => ini_get('upload_max_filesize'),
            'PHP Post Max Size' => ini_get('post_max_size'),
            'PHP Upload Max Filesize' => ini_get('upload_max_filesize'),
            'PHP Time Limit' => ini_get('max_execution_time'),
            'PHP Max Input Vars' => ini_get('max_input_vars'),
            'PHP Arg Separator' => ini_get('arg_separator.output'),
            'PHP Allow URL File Open' => ini_get('allow_url_fopen') ? 'Yes' : 'No',
            'Session' => isset($_SESSION) ? 'Enabled' : 'Disabled',
            'Session Name' => ini_get('session.name'),
            'Cookie Path' => ini_get('session.cookie_path'),
            'Save Path' => ini_get('session.save_path'),
            'Use Cookies' => ini_get('session.use_cookies') ? 'On' : 'Off',
            'Use Only Cookies' => ini_get('session.use_only_cookies') ? 'On' : 'Off',
            'Display Errors' => ini_get('display_errors') ? 'On' : 'Off',
            'FSOCKOPEN' => function_exists('fsockopen') ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.',
            'cURL' => function_exists('curl_init') ? 'Your server supports cURL.' : 'Your server does not support cURL.',
            'SOAP Client' => class_exists('SoapClient') ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.',
        ];

        $section = apply_filters('aff_common_admin_system_info_generate_php_and_mysql', $section);
        Assert_Helper::is_array($section, __METHOD__, 'Expected the "PHP and MySQL" section to be an array. Got: %s', '0.9.9');

        return $section;
    }

	/**
	 * Get the active plugins section for the system info.
	 *
	 * @since 0.9.9
	 * @return array
	 */
	protected function get_active_plugins_section()
	{
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$active_plugins = get_option('active_plugins', []);
		$section = [];

		foreach ($plugins as $plugin_path => $plugin) {
			// Skip not active plugins.
			if (!in_array($plugin_path, $active_plugins)) {
				continue;
			}

			$section[$plugin['Name']] = 'Version: ' . $plugin['Version'];
		}

		$section = apply_filters('aff_common_admin_system_info_generate_active_plugins', $section);
		Assert_Helper::is_array($section, __METHOD__, 'Expected the "Active Plugins" section to be an array. Got: %s', '0.9.9');

		return $section;
	}

	/**
	 * Get the network active plugins section for the system info.
	 *
	 * @since 0.9.9
	 * @return array
	 */
	protected function get_network_active_plugins_section()
	{
		if (!function_exists('wp_get_active_network_plugins')) {
			require_once ABSPATH . 'wp-includes/ms-load.php';
		}

		$plugins = wp_get_active_network_plugins();
		$active_plugins = get_site_option('active_sitewide_plugins', []);
		$section = [];

		foreach($plugins as $plugin_path) {
			$plugin_base = plugin_basename($plugin_path);

			// Skip not active plugins.
			if (!array_key_exists($plugin_base, $active_plugins)) {
				continue;
			}

			$plugin = get_plugin_data( $plugin_path );

			$section[$plugin['Name']] = 'Version: ' . $plugin['Version'];
		}

		$section = apply_filters('aff_common_admin_system_info_generate_network_active_plugins', $section);
		Assert_Helper::is_array($section, __METHOD__, 'Expected the "Network Active Plugins" section to be an array. Got: %s', '0.9.9');

		return $section;
	}

	/**
	 * Get the product slug used to build the product URLs.
	 *
	 * @since 0.9.20
	 * @return string
	 */
	protected function get_product_slug()
	{
		$product_slug = carbon_get_theme_option('affilicious_options_product_container_general_tab_slug_field');
		if(empty($product_slug)) {
			$product_slug = Product::SLUG;
		}

		return $product_slug;
	}

	/**
	 * Check if the log table is installed.
	 *
	 * @since 0.9.20
	 * @return bool
	 */
	protected function is_log_table_installed()
	{
		global $wpdb;

		$table_name = Logs_Table_Creator::get_table_name();
		$exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name;

		return $exists;
	}

    /**
     * Count all available products.
     *
     * @since 0.9.18
     * @return int
     */
	protected function count_all_products()
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s", Product::POST_TYPE);
        $result = $wpdb->get_row($query, ARRAY_N);

        return !empty($result[0]) ? intval($result[0]) : 0;
    }

	/**
	 * Count all trashed products.
	 *
	 * @since 0.9.20
	 * @return int
	 */
    protected function count_all_trashed_products()
    {
	    global $wpdb;

	    $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'trash'", Product::POST_TYPE);
	    $result = $wpdb->get_row($query, ARRAY_N);

	    return !empty($result[0]) ? intval($result[0]) : 0;
    }

    /**
     * Count all orphaned product variants.
     *
     * @since 0.9.18
     * @return int
     */
    protected function count_orphaned_product_variants()
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} product_variants LEFT JOIN {$wpdb->posts} parent_products ON parent_products.id = product_variants.post_parent WHERE product_variants.post_type = %s AND product_variants.post_parent > 0 AND parent_products.id IS NULL", Product::POST_TYPE);
        $result = $wpdb->get_row($query, ARRAY_N);

        return !empty($result[0]) ? intval($result[0]) : 0;
    }

	/**
	 * Get the counter of the update semaphore based on the update interval.
	 *
	 * @since 0.9.20
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return string|null
	 */
    protected function get_update_semaphore_counter($update_interval)
    {
	    $counter_option = Update_Semaphore::$counter_options[$update_interval];
    	$counter = get_option($counter_option);
    	if(empty($counter)) {
    		return null;
	    }

	    return $counter;
    }

    /**
	 * Get the last acquire time of the update semaphore based on the update interval.
	 *
	 * @since 0.9.20
     * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @param bool $to_local_time
	 * @return string|null
	 */
    protected function get_update_semaphore_last_acquire_time($update_interval, $to_local_time = false)
    {
	    $last_acquire_time_option = Update_Semaphore::$last_acquire_time_options[$update_interval];
    	$last_acquire_time = get_option($last_acquire_time_option);
    	if(empty($last_acquire_time)) {
    		return null;
	    }

	    // Convert to the current timezone.
	    if ($to_local_time) {
		    $last_acquire_time = get_date_from_gmt($last_acquire_time, 'Y-m-d H:i:s');
	    }

	    return $last_acquire_time;
    }

	/**
	 * Count all records (rows) in the log table.
	 *
	 * @since 0.9.20
	 * @return int
	 */
    protected function count_log_table_records()
    {
	    global $wpdb;

	    $table = Logs_Table_Creator::get_table_name();
	    $query = "SELECT COUNT(*) FROM {$table}";
	    $result = $wpdb->get_row($query, ARRAY_N);

	    return !empty($result[0]) ? intval($result[0]) : 0;
    }
}
