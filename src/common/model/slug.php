<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

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
}
