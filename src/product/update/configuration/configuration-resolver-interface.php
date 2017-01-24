<?php
namespace Affilicious\Product\Update\Configuration;

use Affilicious\Product\Update\Queue\Update_Queue_Interface;
use Affilicious\Product\Update\Task\Update_Task_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Configuration_Resolver_Interface
{
    /**
     * @since 0.7
     * @param Configuration_Context_Interface $context
     */
    public function __construct(Configuration_Context_Interface $context);

    /**
     * Resolve the configuration for the queue.
     *
     * @since 0.7
     * @param Update_Queue_Interface $queue
     * @param Configuration_Interface $configuration
     * @return Update_Task_Interface[]
     */
    public function resolve(Configuration_Interface $configuration, Update_Queue_Interface $queue);
}
