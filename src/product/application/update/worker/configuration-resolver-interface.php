<?php
namespace Affilicious\Product\Application\Update\Worker;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Configuration_Resolver_Interface
{
    const UPDATE_INTERVAL = 'update_interval';
    const FORCE_UPDATE_INTERVAL = 'force_update_interval';
    const MIN_TASKS = 'min_tasks';
    const MAX_TASKS = 'max_tasks';

    /**
     * Create a new configuration with default values.
     *
     * @since 0.7
     * @param array $defaults
     */
    public function __construct(array $defaults = array());

    /**
     * Check by the key if the configuration does exist.
     *
     * @since 0.7
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Set a new configuration for the key.
     *
     * @since 0.7
     * @param string $key
     * @param mixed $value
     * @return Configuration_Resolver_Interface
     */
    public function set($key, $value);

    /**
     * Delete an existing configuration by the key.
     *
     * @since 0.7
     * @param string $key
     * @return Configuration_Resolver_Interface
     */
    public function delete($key);

    /**
     * Get the configuration by the key.
     *
     * @since 0.7
     * @param string $key
     * @return null|mixed
     */
    public function get($key);

    /**
     * Get all configurations.
     *
     * @since 0.7
     * @return array
     */
    public function get_all();
}
