<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Content
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
	    Assert_Helper::string_or_null($value, __METHOD__, 'The content must be a string or null. Got: %s', '0.9.2');

        $this->set_value($value);
    }
}
