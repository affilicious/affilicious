<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Slug
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @inheritdoc
	 * @since 0.8
	 */
	public function __construct($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The slug must be a string. Got: %s', '0.9.2');

		$this->set_value($value);
	}

	/**
	 * Prefix the name.
	 *
	 * @since 0.9.7
	 * @param string $value
	 * @return Slug
	 */
	public function prefix($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The slug prefix must be a non empty string. Got: %s', '0.9.7');

		return new self($value . $this->value);
	}

	/**
	 * Postfix the name.
	 *
	 * @since 0.9.7
	 * @param string $value
	 * @return Slug
	 */
	public function postfix($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The slug postfix must be a non empty string. Got: %s', '0.9.7');

		return new self($this->value . $value);
	}
}
