<?php
namespace Affilicious\Common\Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.9
 */
interface Timer_Interface
{
	/**
	 * @since 0.9.9
	 * @var string
	 */
	const HOURLY = 'hourly';

	/**
	 * @since 0.9.9
	 * @var string
	 */
	const TWICE_DAILY = 'twicedaily';

	/**
	 * @since 0.9.9
	 * @var string
	 */
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
