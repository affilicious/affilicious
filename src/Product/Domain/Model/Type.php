<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidValueException;

class Type extends AbstractValueObject
{
	const SIMPLE = 'simple';
	const VARIANTS = 'variants';

	/**
	 * @since 0.6
	 * @return Type
	 */
	public static function simple()
	{
		return new self(self::SIMPLE);
	}

	/**
	 * @since 0.6
	 * @return Type
	 */
	public static function complex()
	{
		return new self(self::VARIANTS);
	}

	/**
	 * @inheritdoc
	 * @throws InvalidValueException
	 */
	public function __construct($value)
	{
	    $types = array(
            self::SIMPLE,
            self::VARIANTS
        );

		if(!in_array($value, $types)) {
			throw new InvalidValueException($value, $types, get_class($this));
		}

		parent::__construct($value);
	}
}
