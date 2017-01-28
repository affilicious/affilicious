<?php
namespace Affilicious\Common\Model;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Key
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
        Assert::stringNotEmpty($value, 'The key must be a non empty string. Got: %s');

		$this->set_value($value);
	}
}
