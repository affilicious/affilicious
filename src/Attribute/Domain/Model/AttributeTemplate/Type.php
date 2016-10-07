<?php
namespace Affilicious\Attribute\Domain\Model\AttributeTemplate;

use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidOptionException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Type extends AbstractValueObject
{
	const TEXT = 'text';
	const NUMBER = 'number';

	/**
	 * @since 0.6
	 * @return Type
	 */
	public static function text()
	{
		return new self(self::TEXT);
	}

	/**
	 * @since 0.6
	 * @return Type
	 */
	public static function number()
	{
		return new self(self::NUMBER);
	}

	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws InvalidOptionException
	 */
	public function __construct($value)
	{
	    $types = array(
	        self::TEXT,
            self::NUMBER
        );

		if (!in_array($value, $types)) {
			throw new InvalidOptionException($value, $types);
		}

		parent::__construct($value);
	}
}
