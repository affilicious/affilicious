<?php
namespace Affilicious\Product\Application\Update\Worker;

use Affilicious\Product\Application\Update\Task\Update_Task_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Worker_Interface
{
    /**
     * @since 0.7
     * @param string $name
     */
    public function __construct($name);

    /**
     * Get the unique name of the worker.
     *
     * @since 0.7
     * @return string
     */
    public function get_name();

    /**
     * Configure the worker for the update.
     *
     * @since 0.7
     * @param Configuration_Resolver_Interface $configuration
     * @return Configuration_Resolver_Interface
     */
    public function configure(Configuration_Resolver_Interface $configuration);

    /**
     * Execute the update tasks.
     *
     * @since 0.7
     * @param Update_Task_Interface[] $update_tasks
     */
    public function execute($update_tasks);
}
