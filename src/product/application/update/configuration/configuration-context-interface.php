<?php
namespace Affilicious\Product\Application\Update\Configuration;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Configuration_Context_Interface
{
    const UPDATE_INTERVAL = 'update_interval';

    const DEFAULT_UPDATE_INTERVAL = 'hourly';

    /**
     * Create a new configuration context with default values.
     *
     * @since 0.7
     * @param array $defaults
     */
    public function __construct(array $defaults = array());

    /**
     * Check by the key if the configuration context does exist.
     *
     * @since 0.7
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Set a new configuration context for the key.
     *
     * @since 0.7
     * @param string $key
     * @param mixed $value
     * @return Configuration_Interface
     */
    public function set($key, $value);

    /**
     * Delete an existing configuration context by the key.
     *
     * @since 0.7
     * @param string $key
     * @return Configuration_Interface
     */
    public function delete($key);

    /**
     * Get the configuration context by the key.
     *
     * @since 0.7
     * @param string $key
     * @return null|mixed
     */
    public function get($key);

    /**
     * Get all configuration contexts.
     *
     * @since 0.7
     * @return array
     */
    public function get_all();
}
