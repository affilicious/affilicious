<?php
namespace Affilicious\Common\Admin\License;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class License_Timer extends Abstract_Timer
{
	/**
	 * @var License_Manager
	 */
	private $license_manager;

	/**
	 * @since 0.9.9
	 * @param License_Manager $license_manager
	 */
    public function __construct(License_Manager $license_manager)
    {
	    $this->license_manager = $license_manager;
    }

    /**
     * Activate all scheduled cron jobs for the license timer.
     *
     * @since 0.9.9
     */
    public function activate()
    {
        $this->add_scheduled_action('aff_common_admin_license_run_checks_daily', 'daily');
    }

    /**
     * Deactivate all existing scheduled cron jobs for the license timer.
     *
     * @since 0.9.9
     */
    public function deactivate()
    {
        $this->remove_scheduled_action('aff_common_admin_license_run_checks_daily');
    }

    /**
     * Run the license checks daily as a cron job.
     *
     * @hook aff_common_admin_license_run_checks_daily
     * @since 0.9.9
     */
    public function run_checks_daily()
    {
        $license_handlers = $this->license_manager->get_license_handlers();
        foreach ($license_handlers as $license_handler) {
        	$item_key = $license_handler->get_item_key();
        	$this->license_manager->check_item($item_key);
        }
    }
}
