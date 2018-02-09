<?php
namespace Affilicious\Common\Logs;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Logs_Cleaner_Timer extends Abstract_Timer
{
	/**
	 * @since 0.9.22
	 * @var Logs_Cleaner
	 */
	private $logs_cleaner;

	/**
	 * @since 0.9.22
	 * @param Logs_Cleaner $logs_cleaner
	 */
	public function __construct(Logs_Cleaner $logs_cleaner)
	{
		$this->logs_cleaner = $logs_cleaner;
	}

	/**
	 * @inheritdoc
	 * @since 0.9.22
	 */
	public function activate($network_wide = false)
	{
		$this->add_scheduled_action('aff_common_logs_clean_up_daily', 'daily', $network_wide);
	}

	/**
	 * @inheritdoc
	 * @since 0.9.22
	 */
	public function deactivate($network_wide = false)
	{
		$this->remove_scheduled_action('aff_common_logs_clean_up_daily', $network_wide);
	}

	/**
	 * Run the logs cleaner daily as cron jobs.
	 *
	 * @hook aff_common_logs_clean_up_daily
	 * @since 0.9.22
	 */
	public function clean_up_daily()
	{
		$this->logs_cleaner->clean_up();
	}
}
