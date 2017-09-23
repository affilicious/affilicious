<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Affiliate_Product_Id
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
    	if(is_numeric($value)) {
    		$value = strval($value);
	    }

    	Assert_Helper::is_string_not_empty($value, __METHOD__, 'The affiliate product ID must be a non empty string. Got: %s', '0.9.2');

        $this->set_value($value);
    }
}
