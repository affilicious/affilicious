<?php
namespace Affilicious\Detail\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;


if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Type
{
	const TEXT = 'text';
	const NUMBER = 'number';
	const FILE = 'file';

    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
     * Build a new text type.
     *
	 * @since 0.68
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
     * Build a new file type.
     *
	 * @since 0.8
	 * @return Type
	 */
	public static function file()
	{
		return new self(self::FILE);
	}

    /**
     * @since 0.8
     * @param string $value
     */
	public function __construct($value)
	{
        $values = apply_filters('affilicious_detail_type_values', array(
            self::TEXT,
            self::NUMBER,
            self::FILE
        ));

        Assert::stringNotEmpty($value, 'The type must be a non empty string. Got: %s');
        Assert::oneOf($value, $values, 'Expected type of: %2$s. Got: %s');

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
     * Check if the type is a file.
     *
     * @since 0.8
     * @return bool
     */
    public function is_file()
    {
        return $this->value === self::FILE;
    }

    /**
     * Get the translated label for the type.
     *
     * @since 0.8
     * @return null|string
     */
    public function get_label()
    {
        switch($this->value) {
            case self::TEXT:
                return __('Text', 'affilicious');
            case self::NUMBER:
                return __('Number', 'affilicious');
            case self::FILE:
                return __('File', 'affilicious');
            default:
                $label = apply_filters('affilicious_detail_type_label', $this->value);
                return $label !== $this->value ? $label : null;
        }
    }
}
