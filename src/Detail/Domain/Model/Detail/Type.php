<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Option_Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Type extends Abstract_Value_Object
{
	const TEXT = 'text';
	const NUMBER = 'number';
	const FILE = 'file';

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
	 * @since 0.6
	 * @return Type
	 */
	public static function file()
	{
		return new self(self::FILE);
	}

	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws Invalid_Option_Exception
	 */
	public function __construct($value)
	{
		if (!in_array($value, array(self::TEXT, self::NUMBER, self::FILE))) {
			throw new Invalid_Option_Exception($value, array(
				self::TEXT,
				self::NUMBER,
				self::FILE
			));
		}

		parent::__construct($value);
	}
}
