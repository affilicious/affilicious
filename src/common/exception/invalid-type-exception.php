<?php
namespace Affilicious\Common\Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Invalid_Type_Exception extends Domain_Exception
{
	/**
	 * @since 0.6
	 * @param mixed $invalid_value
	 * @param string $valid_type
	 */
	public function __construct($invalid_value, $valid_type)
	{
		parent::__construct(sprintf(
			'Invalid type %s. Please use %s',
			is_object($invalid_value) ? get_class($invalid_value) : gettype($invalid_value),
			$valid_type
		));
	}
}
