<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.0 Use Affiliate_Product_Id instead.
 */
class Affiliate_Id
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
        Assert::stringNotEmpty($value, 'The affiliate ID must be a non empty string. Got: %s');

		$this->set_value($value);
	}
}
