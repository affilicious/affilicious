<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractValueObject implements ValueObjectInterface
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getValue() === $object->getValue();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return strval($this->value);
    }
}
