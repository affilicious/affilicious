<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Value_Object implements Value_Object_Interface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @inheritdoc
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @inherit_doc
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_value() === $object->get_value();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return strval($this->value);
    }
}
