<?php
namespace Affilicious\Common\Admin\License;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.9
 */
final class License_Timer extends Abstract_Timer
{
	/**
	 * @since 0.9.9
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
     * @inheritdoc
     * @since 0.9.9
     */
    public function activate($network_wide = false)
    {
        $this->add_scheduled_action('aff_common_admin_license_run_checks_daily', 'daily', $network_wide);
    }

    /**
     * @inheritdoc
     * @since 0.9.9
     */
    public function deactivate($network_wide = false)
    {
        $this->remove_scheduled_action('aff_common_admin_license_run_checks_daily', $network_wide);
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
