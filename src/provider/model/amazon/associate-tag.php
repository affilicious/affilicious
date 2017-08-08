<?php
namespace Affilicious\Provider\Model\Amazon;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Associate_Tag
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * @since 0.8
     * @param string $value
     */
    public function __construct($value)
    {
	    Assert_Helper::is_string_not_empty($value, __METHOD__, 'Expected associate tag to be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
    }
}
