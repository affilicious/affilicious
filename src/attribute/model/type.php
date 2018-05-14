<?php
namespace Affilicious\Attribute\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Type
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @since 0.8
	 * @var string
	 */
	const TEXT = 'text';

	/**
	 * @since 0.8
	 * @var string
	 */
	const NUMBER = 'number';

	/**
	 * @since 0.8
	 * @var array
	 */
	public static $all = [
		self::TEXT,
		self::NUMBER
	];

	/**
     * Build a new text type.
     *
	 * @since 0.8
	 * @return Type
	 */
	public static function text()
	{
		return new self(self::TEXT);
	}

	/**
     * Build a new number type.
     *
	 * @since 0.8
	 * @return Type
	 */
	public static function number()
	{
		return new self(self::NUMBER);
	}

    /**
     * @since 0.8
     * @param string $value
     */
	public function __construct($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The type must be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
	}

    /**
     * Check if the type is a text.
     *
     * @since 0.8
     * @return bool
     */
	public function is_text()
    {
        return $this->value === self::TEXT;
    }

    /**
     * Check if the type is a number.
     *
     * @since 0.8
     * @return bool
     */
    public function is_number()
    {
        return $this->value === self::NUMBER;
    }

    /**
     * Get the translated label for the type.
     *
     * @since 0.8
     * @return null|string The translated label if any.
     */
    public function get_label()
    {
        switch($this->value) {
            case self::TEXT:
                $label = __('Text', 'affilicious');
                break;
            case self::NUMBER:
                $label = __('Number', 'affilicious');
                break;
            default:
                $label = null;
        }

	    $label = apply_filters('aff_attribute_type_label', $label, $this->value);

        return $label;
    }
}
