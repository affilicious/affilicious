<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Option_Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Type extends Abstract_Value_Object
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
	 * @throws Invalid_Option_Exception
	 */
	public function __construct($value)
	{
	    $types = array(
	        self::TEXT,
            self::NUMBER
        );

		if (!in_array($value, $types)) {
			throw new Invalid_Option_Exception($value, $types);
		}

		parent::__construct($value);
	}
}
