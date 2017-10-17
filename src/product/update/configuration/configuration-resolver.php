<?php
namespace Affilicious\Product\Update\Configuration;

use Affilicious\Product\Update\Queue\Update_Queue_Interface;
use Affilicious\Product\Update\Task\Batch_Update_Task;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Configuration_Resolver
{
    /**
     * Resolve the configuration for the current context.
     *
     * @since 0.9
     * @param Configuration $configuration
     * @param Configuration_Context $configuration_context
     * @return null|Batch_Update_Task|\WP_Error
     */
    public function resolve(Configuration $configuration, Configuration_Context $configuration_context)
    {
        $is_valid = $configuration->validate();
        if($is_valid instanceof \WP_Error) {
            return $is_valid;
        }

        $is_valid = $configuration_context->validate();
        if($is_valid instanceof \WP_Error) {
            return $is_valid;
        }

        $resolved =
            $this->resolve_update_interval($configuration, $configuration_context) &&
            $this->resolve_min_tasks($configuration, $configuration_context);

        if(!$resolved) {
            return null;
        }

        /** @var Update_Queue_Interface $queue */
        $queue = $configuration_context->get(Configuration_Context::QUEUE);
        $max_tasks = $configuration->get(Configuration::MAX_TASKS);
        $batch_update_tasks = $queue->get_batched($max_tasks);

        return $batch_update_tasks;
    }

    /**
     * @since 0.7
     * @param Configuration $configuration
     * @param Configuration_Context $configuration_context
     * @return bool
     */
    private function resolve_update_interval(Configuration $configuration, Configuration_Context $configuration_context)
    {
        $context_update_interval = $configuration_context->get('update_interval');
        $update_interval = $configuration->get('update_interval');

        $resolved = $context_update_interval == $update_interval;

        return $resolved;
    }

    /**
     * @since 0.7
     * @param Configuration $configuration
     * @param Configuration_Context $configuration_context
     * @return bool
     */
    private function resolve_min_tasks(Configuration $configuration, Configuration_Context $configuration_context)
    {
        /** @var Update_Queue_Interface $queue */
        $queue = $configuration_context->get('queue');

        $min_tasks = $configuration->get(Configuration::MIN_TASKS);
        $size = $queue->get_size();
        $resolved = $size >= $min_tasks;

        return $resolved;
    }
}
