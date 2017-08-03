<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

trait Custom_Value_Aware_Trait
{
	/**
	 * @param array
	 */
	protected $custom_values = [];

	/**
	 * Check if the custom value with the key does exist.
	 *
	 * @since 0.9
	 * @param string $key
	 * @return bool
	 */
	public function has_custom_value($key)
	{
		return isset($this->custom_values[$key]);
	}

	/**
	 * Add custom value to the model which will be not persisted.
	 *
	 * @since 0.9
	 * @param string $key
	 * @param mixed $value
	 */
	public function add_custom_value($key, $value)
	{
		$this->custom_values[$key] = $value;
	}

	/**
	 * Remove custom value from the model.
	 *
	 * @since 0.9
	 * @param string $key
	 */
	public function remove_custom_value($key)
	{
		unset($this->custom_values[$key]);
	}

	/**
	 * Get the custom value by the key.
	 *
	 * @since 0.9
	 * @param string $key
	 * @return mixed|null
	 */
	public function get_custom_value($key)
	{
		return isset($this->custom_values[$key]) ? $this->custom_values[$key] : null;
	}

	/**
	 * Check if any custom values do exists.
	 *
	 * @since 0.9
	 * @return bool
	 */
	public function has_custom_values()
	{
		return !empty($this->custom_values);
	}

	/**
	 * Get the custom values.
	 *
	 * @since 0.9
	 * @return array
	 */
	public function get_custom_values()
	{
		return $this->custom_values;
	}

	/**
	 * Set all custom values.
	 *
	 * @since 0.9
	 * @param array $custom_values
	 */
	public function set_custom_values(array $custom_values)
	{
		$this->custom_values = $custom_values;
	}
}
