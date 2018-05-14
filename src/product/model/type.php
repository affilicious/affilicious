<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

/**
 * @since 0.6
 */
class Type
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @since 0.6
	 * @var string
	 */
	const SIMPLE = 'simple';

	/**
	 * @since 0.6
	 * @var string
	 */
	const COMPLEX = 'complex';

	/**
	 * @since 0.6
	 * @var string
	 */
	const VARIANT = 'variant';

	/**
	 * @since 0.6
	 * @var array
	 */
	public static $all = [
		self::SIMPLE,
		self::COMPLEX,
		self::VARIANT
	];

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
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The type must be a non empty string. Got: %s', '0.9.2');

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

	/**
	 * Get the translated label of the type.
	 *
	 * @since 0.9.2
	 * @return null|string The translated label if any.
	 */
    public function get_label()
    {
    	switch($this->value) {
		    case self::SIMPLE:
		    	$label = __('Simple', 'affilicious');
		    	break;
		    case self::VARIANT:
			    $label = __('Variant', 'affilicious');
			    break;
		    case self::COMPLEX:
			    $label = __('Complex', 'affilicious');
			    break;
		    default:
		    	$label = null;
	    }

	    $label = apply_filters('aff_product_type_label', $label, $this->value);

    	return $label;
    }
}
