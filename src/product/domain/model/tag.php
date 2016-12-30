<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Tag extends Abstract_Value_Object
{
    /**
     * @inheritdoc
     * @since 0.7.1
     * @throws Invalid_Type_Exception
     */
    public function __construct($value)
    {
        $value = strval($value);

        if (!is_string($value)) {
            throw new Invalid_Type_Exception($value, 'int');
        }

        parent::__construct($value);
    }
}
