<?php
namespace Affilicious\Common\Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Assert_Helper
{
	/**
	 * Check if the value is null.
	 *
	 * @since 0.9.4
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is not null.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_not_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a non empty string.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_string_not_empty($value, $method, $message, $version)
	{
		if(WP_DEBUG && (empty($value) || !is_string($value))) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a non empty string or null.
	 *
	 * @since 0.9.2
	 * @param $value
	 * @param $method
	 * @param $message
	 * @param $version
	 */
	public static function is_string_not_empty_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && ((is_string($value) && empty($value)) || !is_string($value) && !is_null($value))) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is an array.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_array($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_array($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is an array or null.
	 *
	 * @since 0.9.2
	 * @param $value
	 * @param $method
	 * @param $message
	 * @param $version
	 */
	public static function is_array_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_array($value) && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is an integer.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_integer($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_int($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is an integer or null.
	 *
	 * @since 0.9.2
	 * @param $value
	 * @param $method
	 * @param $message
	 * @param $version
	 */
	public static function is_integer_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_int($value) && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a boolean.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_boolean($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_bool($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a boolean or null.
	 *
	 * @since 0.9.2
	 * @param $value
	 * @param $method
	 * @param $message
	 * @param $version
	 */
	public static function is_boolean_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_bool($value) && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a float
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function is_float($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_float($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is a float or null.
	 *
	 * @since 0.9.2
	 * @param $value
	 * @param $method
	 * @param $message
	 * @param $version
	 */
	public static function is_float_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_float($value) && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the value is in the given min and max range.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param int $min
	 * @param int $max
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function range($value, $min, $max, $method, $message, $version)
	{
		 if (WP_DEBUG && ($value < $min || $value > $max)) {
			 _doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		 }
	}

	/**
	 * Check if the value is greater than the min value.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param int $min
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function greater_than($value, $min, $method, $message, $version)
	{
		if (WP_DEBUG && !($value > $min)) {
			_doing_it_wrong($method, sprintf($message, $min, $value), $version);
		}
	}

	/**
	 * Check if the value is greater than or equal the min value.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param int $min
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function greater_than_or_equal($value, $min, $method, $message, $version)
	{
		if (WP_DEBUG && !($value >= $min)) {
			_doing_it_wrong($method, sprintf($message, $min, $value), $version);
		}
	}

	/**
	 * Check if the value is less than the min value.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param int $max
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function less_than($value, $max, $method, $message, $version)
	{
		if (WP_DEBUG && !($value < $max)) {
			_doing_it_wrong($method, sprintf($message, $max, $value), $version);
		}
	}

	/**
	 * Check if the value is less than or equal the min value.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param int $max
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function less_than_or_equal($value, $max, $method, $message, $version)
	{
		if (WP_DEBUG && !($value <= $max)) {
			_doing_it_wrong($method, sprintf($message, $max, $value), $version);
		}
	}

	/**
	 * Check if the value is a string or null.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function string_or_null($value, $method, $message, $version)
	{
		if(WP_DEBUG && !is_string($value) && !is_null($value)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the values are all instance of the class.
	 *
	 * @since 0.9.2
	 * @param array $values
	 * @param string $class
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function all_is_instance_of(array $values, $class, $method, $message, $version)
	{
		if(WP_DEBUG) {
			$is_all_instance_of = true;
			$wrong_value = null;

			foreach ($values as $value) {
				if(!($value instanceof $class)) {
					$is_all_instance_of = false;
					$wrong_value = $value;
					break;
				}
			}

			if(!$is_all_instance_of) {
				_doing_it_wrong($method, sprintf($message, self::type_to_string($wrong_value)), $version);
			}
		}
	}

	/**
	 * Check if the value is one of the multiple values.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @param array $values
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function one_of($value, array $values, $method, $message, $version)
	{
		if(WP_DEBUG && !in_array($value, $values)) {
			_doing_it_wrong($method, sprintf($message, self::type_to_string($value)), $version);
		}
	}

	/**
	 * Check if the key exists in the array.
	 *
	 * @since 0.9.2
	 * @param array $array
	 * @param string $key
	 * @param string $method
	 * @param string $message
	 * @param string $version
	 */
	public static function key_exists(array $array, $key, $method, $message, $version)
	{
		if(WP_DEBUG && !array_key_exists($key, $array)) {
			_doing_it_wrong($method, sprintf($message, $key), $version);
		}
	}

	/**
	 * Convert the type to a string.
	 *
	 * @since 0.9.2
	 * @param mixed $value
	 * @return string
	 */
	protected static function type_to_string($value)
	{
		return is_object($value) ? get_class($value) : gettype($value);
	}
}
