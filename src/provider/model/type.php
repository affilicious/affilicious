<?php
namespace Affilicious\Provider\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Type
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @inheritdoc
	 * @since 0.9.7
	 */
	public function __construct($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The type must be a non empty string. Got: %s', '0.9.7');

		$this->set_value($value);
	}
}
