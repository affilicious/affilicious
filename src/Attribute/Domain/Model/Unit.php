<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractValueObject;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Unit extends AbstractValueObject
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
