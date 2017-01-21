<?php
namespace Affilicious\Product\Update;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Timer_Interface
{
    const HOURLY = 'hourly';
    const TWICE_DAILY = 'twicedaily';
    const DAILY = 'daily';

    /**
     * Activate all scheduled events for the workers.
     *
     * @since 0.7
     */
    public function activate();

    /**
     * Deactivate all existing scheduled events from the workers.
     *
     * @since 0.7
     */
    public function deactivate();

    /**
     * Run the worker tasks hourly as cron jobs.
     *
     * @since 0.7
     */
    public function run_tasks_hourly();

    /**
     * Run then worker tasks twice a day as cron jobs.
     *
     * @since 0.7
     */
    public function run_tasks_twice_daily();

    /**
     * Run the worker tasks daily as a cron job.
     *
     * @since 0.7
     */
    public function run_tasks_daily();
}
