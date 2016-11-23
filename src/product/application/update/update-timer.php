<?php
namespace Affilicious\Product\Application\Update;

use Affilicious\Product\Application\Update\Manager\Update_Manager_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Timer implements Update_Timer_Interface
{
    /**
     * @var Update_Manager_Interface
     */
    protected $manager;

    /**
     * @since 0.7
     * @param Update_Manager_Interface $manager
     */
    public function __construct(Update_Manager_Interface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function activate()
    {
        $this->add_scheduled_action('affilicious_product_update_run_tasks_hourly', 'hourly');
        $this->add_scheduled_action('affilicious_product_update_run_tasks_twice_daily', 'twicedaily');
        $this->add_scheduled_action('affilicious_product_update_run_tasks_daily', 'daily');
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function deactivate()
    {
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_hourly');
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_twice_daily');
        $this->remove_scheduled_action('affilicious_product_update_run_tasks_daily');
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function run_tasks_hourly()
    {
        $this->manager->run_tasks(self::HOURLY);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function run_tasks_twice_daily()
    {
        $this->manager->run_tasks(self::TWICE_DAILY);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function run_tasks_daily()
    {
        $this->manager->run_tasks(self::DAILY);
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
