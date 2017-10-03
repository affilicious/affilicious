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
	 */
	public function activate();

	/**
	 * Deactivate all existing scheduled cron jobs.
	 *
	 * @since 0.9.9
	 */
	public function deactivate();
}
