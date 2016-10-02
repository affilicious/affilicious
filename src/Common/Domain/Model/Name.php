<?php
namespace Affilicious\Common\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Name extends AbstractValueObject
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws InvalidTypeException
	 */
	public function __construct($value)
	{
		if (!is_string($value)) {
			throw new InvalidTypeException($value, 'string');
		}

		parent::__construct($value);
	}
}
