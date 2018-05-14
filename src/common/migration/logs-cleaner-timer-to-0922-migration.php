<?php
namespace Affilicious\Common\Migration;

use Affilicious\Common\Cleaner\Logs_Cleaner_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Logs_Cleaner_Timer_to_0922_Migration
{
	/**
	 * @since 0.9.22
	 * @var string
	 */
    const OPTION = 'aff_migrated_logs_cleaner_timer_to_0.9.22';

	/**
	 * @since 0.9.22
	 * @var Logs_Cleaner_Timer
	 */
	private $logs_cleaner_timer;

	/**
	 * @since 0.9.22
	 * @param Logs_Cleaner_Timer $logs_cleaner_timer
	 */
	public function __construct(Logs_Cleaner_Timer $logs_cleaner_timer)
	{
		$this->logs_cleaner_timer = $logs_cleaner_timer;
	}

	/**
     * @since 0.9.22
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.9.22' && get_option(self::OPTION) != 'yes') {
	        $this->logs_cleaner_timer->activate();

            update_option(self::OPTION, 'yes');
       }
    }
}
