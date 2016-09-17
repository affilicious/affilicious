<?php
namespace Affilicious\Common\Domain\Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class InvalidTypeException extends DomainException
{
	/**
	 * @since 0.5.2
	 * @param mixed $invalidValue
	 * @param string $validType
	 */
	public function __construct($invalidValue, $validType)
	{
		parent::__construct(sprintf(
			__('Invalid type %s. Please use %s', 'affilicious'),
			gettype($invalidValue),
			$validType
		));
	}
}
