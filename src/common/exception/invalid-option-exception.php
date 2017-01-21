<?php
namespace Affilicious\Product\Exception;

use Affilicious\Common\Exception\Domain_Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Invalid_Option_Exception extends Domain_Exception
{
	/**
	 * @since 0.6
	 * @param mixed $invalid_option
	 * @param array[] $valid_options
	 */
	public function __construct($invalid_option, $valid_options)
	{
		parent::__construct(sprintf(
			'Invalid option %s. Please choose from %s.',
			$invalid_option,
			implode(', ', $valid_options)
		));
	}
}
