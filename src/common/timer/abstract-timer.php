<?php
namespace Affilicious\Common\Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

abstract class Abstract_Timer implements Timer_Interface
{
	/**
	 * Add a new scheduled cron job action to the single site or complete multisite.
	 *
	 * @since 0.9.9
	 * @param string $hook The name of the hook for the cron job.
	 * @param string $recurrence How often the cron job should reoccur like "hourly", "twicedaily" or "daily".
	 * @param bool $network_wide Optional. Remove this action from all sites in the multisite. Default: false
	 * @param array $args Optional. Arguments that were to be passed to the hook's callback function. Default: empty
	 */
	protected function add_scheduled_action($hook, $recurrence, $network_wide = false, array $args = [])
	{
		global $wpdb;

		// Just a quick fix for the "wrong" spelling of "twicedaily"...
		if($recurrence == 'twice_daily') {
			$recurrence = self::TWICE_DAILY;
		}

		// Check if the action has to be installed for the complete multisite.
		if($network_wide && is_multisite()) {
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);

				if (!wp_next_scheduled($hook)) {
					wp_schedule_event(time(), $recurrence, $hook, $args);
				}

				restore_current_blog();
			}
		} else {
			if (!wp_next_scheduled($hook)) {
				wp_schedule_event(time(), $recurrence, $hook, $args);
			}
		}
	}

	/**
	 * Remove an existing scheduled cron job action from the single site or complete multisite.
	 *
	 * @since 0.9.9
	 * @param string $hook Action hook, the execution of which will be unscheduled.
	 * @param bool $network_wide Optional. Remove this action from all sites in the multisite. Default: false
	 * @param array $args Optional. Arguments that were to be passed to the hook's callback function. Default: empty
	 */
	protected function remove_scheduled_action($hook, $network_wide= false, array $args = [])
	{
		global $wpdb;

		// Check if the action has to be removed for the complete multisite.
		if($network_wide && is_multisite()) {
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);

				wp_clear_scheduled_hook($hook, $args);

				restore_current_blog();
			}
		} else {
			wp_clear_scheduled_hook($hook, $args);
		}
	}
}
