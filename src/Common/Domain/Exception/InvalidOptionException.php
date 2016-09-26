<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class InvalidOptionException extends DomainException
{
	/**
	 * @since 0.3
	 * @param mixed $invalidOption
	 * @param array[] $validOptions
	 */
	public function __construct($invalidOption, $validOptions)
	{
		parent::__construct(sprintf(
			'Invalid option %s. Please choose from %s.',
			$invalidOption,
			implode(', ', $validOptions)
		));
	}
}
