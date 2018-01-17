<?php
namespace Affilicious\Common\Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface Timer_Interface
{
	const HOURLY = 'hourly';
	const TWICE_DAILY = 'twicedaily';
	const DAILY = 'daily';

	/**
	 * Activate all scheduled cron jobs.
	 *
	 * @since 0.9.9
	 * @param bool $network_wide Optional. Activate the timer for the complete multisite. Default: false.
	 */
	public function activate($network_wide = false);

	/**
	 * Deactivate all existing scheduled cron jobs.
	 *
	 * @since 0.9.9
	 * @param bool $network_wide Optional. Activate the timer for the complete multisite. Default: false.
	 */
	public function deactivate($network_wide = false);
}
