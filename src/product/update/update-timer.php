<?php
namespace Affilicious\Product\Update;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Update_Timer extends Abstract_Timer
{
    /**
     * @var Update_Manager
     */
    private $update_manager;

	/**
	 * @var Update_Semaphore
	 */
	private $update_semaphore;

	/**
	 * @since 0.7
	 * @param Update_Manager $update_manager The update manager creates and runs all tasks.
	 * @param Update_Semaphore $update_semaphore The binary semaphore prevents multiple updates which are running in parallel.
	 */
    public function __construct(Update_Manager $update_manager, Update_Semaphore $update_semaphore)
    {
        $this->update_manager = $update_manager;
	    $this->update_semaphore = $update_semaphore;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function activate($network_wide = false)
    {
        $this->add_scheduled_action('aff_product_update_run_tasks_hourly', 'hourly', $network_wide);
        $this->add_scheduled_action('aff_product_update_run_tasks_twice_daily', 'twicedaily', $network_wide);
        $this->add_scheduled_action('aff_product_update_run_tasks_daily', 'daily', $network_wide);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function deactivate($network_wide = false)
    {
        $this->remove_scheduled_action('aff_product_update_run_tasks_hourly', $network_wide);
        $this->remove_scheduled_action('aff_product_update_run_tasks_twice_daily', $network_wide);
        $this->remove_scheduled_action('aff_product_update_run_tasks_daily', $network_wide);
    }

    /**
     * Run the update worker tasks hourly as cron jobs.
     *
     * @hook aff_product_update_run_tasks_hourly
     * @since 0.7
     */
    public function run_tasks_hourly()
    {
    	if($this->update_semaphore->acquire()) {
		    $this->update_manager->run_tasks(self::HOURLY);
		    $this->update_semaphore->release();
	    }
    }

    /**
     * Run then worker tasks twice a day as cron jobs.
     *
     * @hook aff_product_update_run_tasks_twice_daily
     * @since 0.7
     */
    public function run_tasks_twice_daily()
    {
	    if($this->update_semaphore->acquire()) {
            $this->update_manager->run_tasks(self::TWICE_DAILY);
		    $this->update_semaphore->release();
	    }
    }

    /**
     * Run the update worker tasks daily as a cron job.
     *
     * @hook aff_product_update_run_tasks_daily
     * @since 0.7
     */
    public function run_tasks_daily()
    {
	    if($this->update_semaphore->acquire()) {
            $this->update_manager->run_tasks(self::DAILY);
		    $this->update_semaphore->release();
        }
    }
}
