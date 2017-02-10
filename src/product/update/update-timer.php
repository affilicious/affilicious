<?php
namespace Affilicious\Product\Update;

use Affilicious\Product\Update\Manager\Update_Manager_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Update_Timer
{
    const HOURLY = 'hourly';
    const TWICE_DAILY = 'twicedaily';
    const DAILY = 'daily';

    /**
     * @var Update_Manager_Interface
     */
    private $update_manager;

    /**
     * @since 0.7
     * @param Update_Manager_Interface $update_manager
     */
    public function __construct(Update_Manager_Interface $update_manager)
    {
        $this->update_manager = $update_manager;
    }

    /**
     * Activate all scheduled events for the workers.
     *
     * @since 0.7
     */
    public function activate()
    {
        $this->add_scheduled_action('affilicious_product_update_run_tasks_hourly', 'hourly');
        $this->add_scheduled_action('affilicious_product_update_run_tasks_twice_daily', 'twicedaily');
        $this->add_scheduled_action('affilicious_product_update_run_tasks_daily', 'daily');
    }

    /**
     * Deactivate all existing scheduled events from the workers.
     *
     * @since 0.7
     */
    public function deactivate()
    {
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_hourly');
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_twice_daily');
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_daily');
    }

    /**
     * Run the worker tasks hourly as cron jobs.
     *
     * @hook affilicious_product_update_run_tasks_hourly
     * @since 0.7
     */
    public function run_tasks_hourly()
    {
        $this->update_manager->run_tasks(self::HOURLY);
    }

    /**
     * Run then worker tasks twice a day as cron jobs.
     *
     * @hook affilicious_product_update_run_tasks_twice_daily
     * @since 0.7
     */
    public function run_tasks_twice_daily()
    {
        $this->update_manager->run_tasks(self::TWICE_DAILY);
    }

    /**
     * Run the worker tasks daily as a cron job.
     *
     * @hook affilicious_product_update_run_tasks_daily
     * @since 0.7
     */
    public function run_tasks_daily()
    {
        $this->update_manager->run_tasks(self::DAILY);
    }

    /**
     * Add a new scheduled action.
     *
     * @since 0.7
     * @param string $hook
     * @param string $recurrence
     */
    protected function add_scheduled_action($hook, $recurrence)
    {
        $recurrences = array(
            self::HOURLY,
            self::TWICE_DAILY,
            self::DAILY
        );

        if($recurrence == 'twice_daily') {
            $recurrence = self::TWICE_DAILY;
        }

        if(!in_array($recurrence, $recurrences)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid recurrence "%s" for the update timer. Please choose from "%s"',
                $recurrence,
                implode(', ', $recurrences)
            ));
        }

        if (!wp_next_scheduled($hook)) {
            wp_schedule_event(time(), $recurrence, $hook);
        }
    }

    /**
     * Remove an existing scheduled action.
     *
     * @since 0.7
     * @param string $hook
     */
    protected function remove_scheduled_action($hook)
    {
        wp_clear_scheduled_hook($hook);
    }
}
