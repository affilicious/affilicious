<?php
namespace Affilicious\Product\Domain\Model\AttributeGroup\Attribute;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractValueObject;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Value extends AbstractValueObject
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws InvalidTypeException
	 */
	public function __construct($value)
	{
		parent::__construct($value);
	}
}
