<?php
namespace Affilicious\Product\Update\Configuration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Configuration
{
	const PROVIDER_SLUG = 'provider_slug';
	const PROVIDER_TYPE = 'provider_type';
    const MIN_TASKS = 'min_tasks';
    const MAX_TASKS = 'max_tasks';
    const DEFAULT_MIN_TASKS = 1;
    const DEFAULT_MAX_TASKS = 10;

    // @deprecated 1.1 Use 'provider_slug' instead.
	const PROVIDER = 'provider';

    /**
     * @var array
     */
    private $values;

    /**
     * Create a new configuration with default values.
     *
     * @since 0.7
     * @param array $defaults The default configuration values with all keys. Default: empty.
     */
    public function __construct(array $defaults = array())
    {
        $this->values = wp_parse_args($defaults, array(
            self::MIN_TASKS => self::DEFAULT_MIN_TASKS,
            self::MAX_TASKS => self::DEFAULT_MAX_TASKS,
        ));
    }

    /**
     * Check by the key if the configuration value does exist.
     *
     * @since 0.7
     * @param string $key The configuration key used for the related value.
     * @return bool Whether a configuration value for the key is existing or not.
     */
    public function has($key)
    {
        return isset($this->values[$key]);
    }

    /**
     * Set a new configuration value for the key.
     *
     * @since 0.7
     * @param string $key The configuration key used for the related value.
     * @param mixed $value The configuration value for the key.
     * @return Configuration Returns the configuration which can be used for fluent interfaces.
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Set all configuration values for the keys.
     *
     * @since 0.9
     * @param array $values The configuration values with all keys.
     * @return Configuration Returns the configuration which can be used for fluent interfaces.
     */
    public function set_all($values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Delete an existing configuration value by the key.
     *
     * @since 0.7
     * @param string $key The configuration key used for the related value.
     * @return Configuration Returns the configuration which can be used for fluent interfaces.
     */
    public function delete($key)
    {
        unset($this->values[$key]);

        return $this;
    }

    /**
     * Get the configuration value by the key.
     *
     * @since 0.7
     * @param string $key The configuration key used for the related value.
     * @return null|mixed The configuration value of the key.
     */
    public function get($key)
    {
        return $this->has($key) ? $this->values[$key] : null;
    }

    /**
     * Get all configuration values.
     *
     * @since 0.7
     * @return array The configuration values with all keys.
     */
    public function get_all()
    {
        return $this->values;
    }

    /**
     * Validate the configuration.
     *
     * @since 0.9
     * @return true|\WP_Error True if the configuration is valid. Otherwise WP_Error if it's invalid.
     */
    public function validate()
    {
        if(!$this->has(self::PROVIDER_SLUG) && !$this->has(self::PROVIDER_TYPE) && !$this->has(self::PROVIDER)) {
            return new \WP_Error('aff_invalid_product_update_configuration', sprintf(
	            __('Invalid configuration. Neither "%s" nor "%s" has been found.', 'affilicious'),
                self::PROVIDER_SLUG,
	            self::PROVIDER_TYPE
            ));
        }

        if(!$this->has(self::MIN_TASKS)) {
            return new \WP_Error('aff_invalid_product_update_configuration', sprintf(
	            __('Invalid configuration. The value for the key "%s" is missing.', 'affilicious'),
                self::MIN_TASKS
            ));
        }

        if(!$this->has(self::MAX_TASKS)) {
            return new \WP_Error('aff_invalid_product_update_configuration', sprintf(
	            __('Invalid configuration. The value for the key "%s" is missing.', 'affilicious'),
                self::MAX_TASKS
            ));
        }

        return true;
    }
}
