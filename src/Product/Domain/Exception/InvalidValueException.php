<?php
namespace Affilicious\Product\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InvalidValueException extends \RuntimeException
{
	/**
	 * @since 0.6
	 * @param mixed $invalidValue
	 * @param array $validValues
	 * @param string $class
	 */
	public function __construct($invalidValue, $validValues, $class)
	{
		parent::__construct(sprintf(
			'Invalid value %s for %s. Please choose from %s',
			$invalidValue,
			implode(',', $validValues),
			$class
		));
	}
}
