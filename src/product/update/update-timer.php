<?php
namespace Affilicious\Product\Update;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Update_Timer extends Abstract_Timer
{
    const HOURLY = 'hourly';
    const TWICE_DAILY = 'twicedaily';
    const DAILY = 'daily';

    /**
     * @var Update_Manager
     */
    private $update_manager;

    /**
     * @since 0.7
     * @param Update_Manager $update_manager The update manager creates and runs all tasks.
     */
    public function __construct(Update_Manager $update_manager)
    {
        $this->update_manager = $update_manager;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function activate()
    {
        $this->add_scheduled_action('aff_product_update_run_tasks_hourly', 'hourly');
        $this->add_scheduled_action('aff_product_update_run_tasks_twice_daily', 'twicedaily');
        $this->add_scheduled_action('aff_product_update_run_tasks_daily', 'daily');
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function deactivate()
    {
        $this->remove_scheduled_action('aff_product_update_run_tasks_hourly');
        $this->remove_scheduled_action('aff_product_update_run_tasks_twice_daily');
        $this->remove_scheduled_action('aff_product_update_run_tasks_daily');
    }

    /**
     * Run the update worker tasks hourly as cron jobs.
     *
     * @hook aff_product_update_run_tasks_hourly
     * @since 0.7
     */
    public function run_tasks_hourly()
    {
        $this->update_manager->run_tasks(self::HOURLY);
    }

    /**
     * Run then worker tasks twice a day as cron jobs.
     *
     * @hook aff_product_update_run_tasks_twice_daily
     * @since 0.7
     */
    public function run_tasks_twice_daily()
    {
        $this->update_manager->run_tasks(self::TWICE_DAILY);
    }

    /**
     * Run the update worker tasks daily as a cron job.
     *
     * @hook aff_product_update_run_tasks_daily
     * @since 0.7
     */
    public function run_tasks_daily()
    {
        $this->update_manager->run_tasks(self::DAILY);
    }
}
