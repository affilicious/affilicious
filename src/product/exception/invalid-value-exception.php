<?php
namespace Affilicious\Product\Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Invalid_Value_Exception extends \RuntimeException
{
	/**
	 * @since 0.6
	 * @param mixed $invalid_value
	 * @param array $valid_values
	 * @param string $class
	 */
	public function __construct($invalid_value, $valid_values, $class)
	{
		parent::__construct(sprintf(
			'Invalid value %s for %s. Please choose from %s',
			$invalid_value,
			implode(',', $valid_values),
			$class
		));
	}
}
