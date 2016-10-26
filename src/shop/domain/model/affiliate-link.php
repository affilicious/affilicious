<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Affiliate_Link extends Abstract_Value_Object
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
