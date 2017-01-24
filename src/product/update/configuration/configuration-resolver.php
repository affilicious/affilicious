<?php
namespace Affilicious\Product\Update\Configuration;

use Affilicious\Product\Update\Queue\Update_Queue_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Configuration_Resolver implements Configuration_Resolver_Interface
{
    /**
     * @var Configuration_Context_Interface
     */
    protected $context;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Configuration_Context_Interface $context)
    {
        $this->context = $context;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function resolve(Configuration_Interface $configuration, Update_Queue_Interface $queue)
    {
        $this->validate_configuration($configuration);

        $resolved =
            $this->resolve_update_interval($configuration, $queue) &&
            $this->resolve_min_tasks($configuration, $queue);

        if(!$resolved) {
            return array();
        }

        $max_tasks = $configuration->get(Configuration_Interface::MAX_TASKS);
        $update_tasks = $queue->get($max_tasks);

        return $update_tasks;
    }

    /**
     * @since 0.7
     * @param Configuration_Interface $configuration
     * @param Update_Queue_Interface $queue
     * @return bool
     */
    protected function resolve_update_interval(Configuration_Interface $configuration, Update_Queue_Interface $queue)
    {
        $context_update_interval = $this->context->get('update_interval');
        $update_interval = $configuration->get('update_interval');

        $resolved = false;
        switch($update_interval) {
            case 'hourly':
                $resolved = in_array($context_update_interval, array('hourly', 'twicedaily', 'daily'));
                break;
            case 'twicedaily':
                $resolved = in_array($context_update_interval, array('twicedaily', 'daily'));
                break;
            case 'daily':
                $resolved = in_array($context_update_interval, array('daily'));
                break;
            default:
                break;
        }

        return $resolved;
    }

    /**
     * @since 0.7
     * @param Configuration_Interface $configuration
     * @param Update_Queue_Interface $queue
     * @return bool
     */
    protected function resolve_min_tasks(Configuration_Interface $configuration, Update_Queue_Interface $queue)
    {
        $min_tasks = $configuration->get(Configuration_Interface::MIN_TASKS);
        $resolved = $queue->get_size() >= $min_tasks;

        return $resolved;
    }

    /**
     * Validate the configuration.
     *
     * @since 0.7
     * @param Configuration_Interface $configuration
     * @throw \InvalidArgumentException
     */
    protected function validate_configuration(Configuration_Interface $configuration)
    {
        if(!$configuration->has(Configuration_Interface::UPDATE_INTERVAL)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration. The key %s is missing.',
                Configuration_Interface::UPDATE_INTERVAL
            ));
        }

        if(!$configuration->has(Configuration_Interface::MIN_TASKS)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration. The key %s is missing.',
                Configuration_Interface::MIN_TASKS
            ));
        }

        if(!$configuration->has(Configuration_Interface::MAX_TASKS)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration. The key %s is missing.',
                Configuration_Interface::MAX_TASKS
            ));
        }
    }
}
