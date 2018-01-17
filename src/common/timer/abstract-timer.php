<?php
namespace Affilicious\Common\Timer;

use Affilicious\Common\Helper\Network_Helper;

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
		// Just a quick fix for the "wrong" spelling of "twicedaily"...
		if($recurrence == 'twice_daily') {
			$recurrence = self::TWICE_DAILY;
		}

		Network_Helper::for_each_blog(function() use ($hook, $recurrence, $args) {
			if (!wp_next_scheduled($hook)) {
				wp_schedule_event(time(), $recurrence, $hook, $args);
			}
		}, $network_wide);
	}

	/**
	 * Remove an existing scheduled cron job action from the single site or complete multisite.
	 *
	 * @since 0.9.9
	 * @param string $hook Action hook, the execution of which will be unscheduled.
	 * @param bool $network_wide Optional. Remove this action from all sites in the multisite. Default: false
	 * @param array $args Optional. Arguments that were to be passed to the hook's callback function. Default: empty
	 */
	protected function remove_scheduled_action($hook, $network_wide = false, array $args = [])
	{
		Network_Helper::for_each_blog(function() use ($hook, $args) {
			wp_clear_scheduled_hook($hook, $args);
		}, $network_wide);
	}
}
