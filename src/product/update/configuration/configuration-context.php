<?php
namespace Affilicious\Product\Update\Configuration;

use Affilicious\Product\Update\Queue\Update_Queue_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Configuration_Context
{
    const QUEUE = 'queue';
    const UPDATE_INTERVAL = 'update_interval';
    const DEFAULT_UPDATE_INTERVAL = 'hourly';

    /**
     * @var array
     */
    private $values;

    /**
     * Create a new configuration context with default values.
     *
     * @since 0.7
     * @param array $defaults The default configuration context values with all keys. Default: empty.
     */
    public function __construct(array $defaults = array())
    {
        $this->values = wp_parse_args($defaults, array(
            self::UPDATE_INTERVAL => self::DEFAULT_UPDATE_INTERVAL,
        ));
    }

    /**
     * Check by the key if the configuration context value does exist.
     *
     * @since 0.7
     * @param string $key The configuration context key used for the related value.
     * @return bool Whether a configuration context value for the key is existing or not.
     */
    public function has($key)
    {
        return isset($this->values[$key]);
    }

    /**
     * Set a new configuration context value for the key.
     *
     * @since 0.7
     * @param string $key The configuration context key used for the related value.
     * @param mixed $value The configuration context value of the key.
     * @return Configuration_Context Returns the configuration context which can be used for fluent interfaces.
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Set all configuration context values.
     *
     * @since 0.9
     * @param array $values
     * @return Configuration_Context Returns the configuration context which can be used for fluent interfaces.
     */
    public function set_all($values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Delete an existing configuration context value by the key.
     *
     * @since 0.7
     * @param string $key The configuration context key used for the related value.
     * @return Configuration_Context Returns the configuration context which can be used for fluent interfaces.
     */
    public function delete($key)
    {
        unset($this->values[$key]);

        return $this;
    }

    /**
     * Get the configuration context value by the key.
     *
     * @since 0.7
     * @param string $key The configuration context key used for the related value.
     * @return null|mixed The configuration context value of the key.
     */
    public function get($key)
    {
        return $this->has($key) ? $this->values[$key] : null;
    }

    /**
     * Get all configuration context values.
     *
     * @since 0.7
     * @return array The configuration context values with all keys.
     */
    public function get_all()
    {
        return $this->values;
    }

    /**
     * Validate the configuration context.
     *
     * @since 0.9
     * @return true|\WP_Error True if the configuration context is valid. Otherwise WP_Error if it's invalid.
     */
    public function validate()
    {
        if(!$this->has(self::QUEUE)) {
            return new \WP_Error('aff_invalid_product_update_configuration_context', sprintf(
                'Invalid configuration context. The value for the key "%s" is missing.',
                self::QUEUE
            ));
        }

        $queue = $this->get('queue');
        if(!($queue instanceof Update_Queue_Interface)) {
            return new \WP_Error('aff_invalid_product_update_configuration_context', sprintf(
                'Invalid configuration context. The value for the key "%s" must implement %s.',
                self::QUEUE,
                Update_Queue_Interface::class
            ));
        }

        if(!$this->has(self::UPDATE_INTERVAL)) {
            return new \WP_Error('aff_invalid_product_update_configuration_context', sprintf(
                'Invalid configuration context. The value for the key %s is missing.',
                self::UPDATE_INTERVAL
            ));
        }

        return true;
    }
}
