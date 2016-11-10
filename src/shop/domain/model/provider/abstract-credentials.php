<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Credentials extends Abstract_Value_Object implements Credentials_Interface
{
    /**
     * @var array
     */
    protected $value;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_value() == $object->get_value();
    }
}
