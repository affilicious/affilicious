<?php
namespace Affilicious\Product\Update;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Update_Timer
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
     * Activate all scheduled events for the update workers.
     *
     * @since 0.7
     */
    public function activate()
    {
        $this->add_scheduled_action('aff_product_update_run_tasks_hourly', 'hourly');
        $this->add_scheduled_action('aff_product_update_run_tasks_twice_daily', 'twicedaily');
        $this->add_scheduled_action('aff_product_update_run_tasks_daily', 'daily');
    }

    /**
     * Deactivate all existing scheduled events from the update workers.
     *
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

    /**
     * Add a new scheduled cron job action.
     *
     * @since 0.7
     * @param string $hook The name of the hook for the cron job.
     * @param string $recurrence How often the cron job should reoccur like "hourly", "twicedaily" or "daily".
     */
    private function add_scheduled_action($hook, $recurrence)
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
     * @since 0.7
     * @param string $hook
     */
    private function remove_scheduled_action($hook)
    {
        wp_clear_scheduled_hook($hook);
    }
}
