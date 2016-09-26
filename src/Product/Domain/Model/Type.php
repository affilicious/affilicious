<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidValueException;

class Type extends AbstractValueObject
{
	const SIMPLE = 'simple';
	const COMPLEX = 'complex';

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
		return new self(self::COMPLEX);
	}

	/**
	 * @inheritdoc
	 * @throws InvalidValueException
	 */
	public function __construct($value)
	{
		if(!in_array($value, array(self::SIMPLE, self::COMPLEX))) {
			throw new InvalidValueException(
				$value,
				array(self::SIMPLE, self::COMPLEX),
				get_class($this)
			);
		}

		parent::__construct($value);
	}
}
