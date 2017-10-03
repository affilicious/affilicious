<?php
namespace Affilicious\Common\Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

abstract class Abstract_Timer implements Timer_Interface
{
	/**
	 * Add a new scheduled cron job action.
	 *
	 * @since 0.9.9
	 * @param string $hook The name of the hook for the cron job.
	 * @param string $recurrence How often the cron job should reoccur like "hourly", "twicedaily" or "daily".
	 */
	protected function add_scheduled_action($hook, $recurrence)
	{
		if($recurrence == 'twice_daily') {
			$recurrence = self::TWICE_DAILY;
		}

		if (!wp_next_scheduled($hook)) {
			wp_schedule_event(time(), $recurrence, $hook);
		}
	}

	/**
	 * Remove an existing scheduled cron job action.
	 *
	 * @since 0.9.9
	 * @param string $hook
	 */
	protected function remove_scheduled_action($hook)
	{
		wp_clear_scheduled_hook($hook);
	}
}
