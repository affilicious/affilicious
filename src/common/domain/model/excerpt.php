<?php
namespace Affilicious\Common\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Excerpt extends Abstract_Value_Object
{
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

        parent::__construct($value);
    }
}
