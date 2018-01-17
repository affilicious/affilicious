<?php
namespace Affilicious\Common\Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Network_Helper
{
	/**
	 * Call the provided callback for each multisite.
	 *
	 * @since 0.9.20
	 * @param callable $callback The callback to be called for each multisite.
	 * @param bool $network_wide Optional. Whether to call the callback on the whole multisite or not. Default: true
	 */
	public static function for_each_blog(callable $callback, $network_wide = true)
	{
		global $wpdb;

		// Check if the action has to be removed for the complete multisite.
		if(is_multisite() && $network_wide) {
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);

				$callback($blog_id);

				restore_current_blog();
			}
		} else {
			$blog_id = get_current_blog_id();

			$callback($blog_id);
		}
	}

	/**
	 * Call the provided callback on the blog with the given ID.
	 *
	 * @since 0.9.20
	 * @param int $blog_id The blog ID we are switching to.
	 * @param callable $callback The callback to be called on the blog.
	 */
	public static function for_blog($blog_id, callable $callback)
	{
		// Check if we are really on a multisite...
		if(!is_multisite()) {
			return;
		}

		switch_to_blog($blog_id);

		$callback($blog_id);

		restore_current_blog();
	}
}
