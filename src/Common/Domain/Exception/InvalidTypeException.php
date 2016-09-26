<?php
namespace Affilicious\Common\Domain\Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class InvalidTypeException extends DomainException
{
	/**
	 * @since 0.6
	 * @param mixed $invalidValue
	 * @param string $validType
	 */
	public function __construct($invalidValue, $validType)
	{
		parent::__construct(sprintf(
			'Invalid type %s. Please use %s',
			gettype($invalidValue),
			$validType
		));
	}
}
