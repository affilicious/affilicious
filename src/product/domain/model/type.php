<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Value_Exception;

class Type extends Abstract_Value_Object
{
	const SIMPLE = 'simple';
	const COMPLEX = 'complex';
	const VARIANT = 'variant';

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
	 * @since 0.6
	 * @return Type
	 */
	public static function variant()
	{
		return new self(self::VARIANT);
	}

	/**
	 * @inheritdoc
     * @since 0.6
	 * @throws Invalid_Value_Exception
	 */
	public function __construct($value)
	{
	    $types = array(
            self::SIMPLE,
            self::COMPLEX,
            self::VARIANT,
        );

		if(!in_array($value, $types)) {
			throw new Invalid_Value_Exception($value, $types, get_class($this));
		}

		parent::__construct($value);
	}

    /**
     * Check if the type is simple.
     *
     * @since 0.7
     * @return bool
     */
	public function is_simple()
    {
        return $this->value === self::SIMPLE;
    }

    /**
     * Check if the type is complex.
     *
     * @since 0.7
     * @return bool
     */
    public function is_complex()
    {
        return $this->value === self::COMPLEX;
    }

    /**
     * Check if the type is complex.
     *
     * @since 0.7
     * @return bool
     */
    public function is_variant()
    {
        return $this->value === self::VARIANT;
    }
}
