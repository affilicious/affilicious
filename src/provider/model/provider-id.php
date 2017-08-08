<?php
namespace Affilicious\Provider\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Provider_Id
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
		if (is_numeric($value) || is_string($value)) {
			$value = intval($value);
		}

		Assert_Helper::is_integer($value, __METHOD__, 'Expected provider ID to be an integer. Got: %s', '0.9.2');

		$this->set_value($value);
	}
}
