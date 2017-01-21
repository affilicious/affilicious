<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Exception\Invalid_Type_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Content
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * @inheritdoc
     * @since 0.6
     * @throws Invalid_Type_Exception
     */
    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new Invalid_Type_Exception($value, 'string');
        }

        $this->set_value($value);
    }
}
