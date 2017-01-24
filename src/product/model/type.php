<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

class Type
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

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
     * @since 0.6
     * @param string $value
     */
	public function __construct($value)
	{
	    $values = apply_filters('affilicious_product_type_values', array(
            self::SIMPLE,
            self::COMPLEX,
            self::VARIANT,
        ));

        Assert::stringNotEmpty($value, 'The type must be a non empty string. Got: %s');
        Assert::oneOf($value, $values, 'Expected type of: %2$s. Got: %s');

		$this->set_value($value);
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
