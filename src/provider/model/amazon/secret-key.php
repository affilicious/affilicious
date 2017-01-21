<?php
namespace Affilicious\Provider\Model\Amazon;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Secret_Key
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
        Assert::string($value, 'Expected secret key to be a string. Got: %s');

        $this->set_value($value);
    }
}
