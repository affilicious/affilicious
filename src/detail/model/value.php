<?php
namespace Affilicious\Detail\Model;

use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Value
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * @since 0.8
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->set_value($value);
    }
}
