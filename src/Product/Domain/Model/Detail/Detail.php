<?php
namespace Affilicious\Product\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail extends AbstractAggregate
{
    /**
     * @var Key
     */
    private $key;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var Name
     */
    private $name;

    /**
     * @var Unit
     */
    private $unit;

    /**
     * @var Value
     */
    private $value;

    /**
     * @since 0.5.2
     * @param Key $key
     * @param Type $type
     * @param Name $name
     */
    public function __construct(Key $key, Type $type, Name $name)
    {
        $this->key = $key;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Get the unique key
     *
     * @since 0.5.2
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the type like text, number or file
     *
     * @since 0.5.2
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the name
     *
     * @since 0.5.2
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if the detail has an unit
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasUnit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the unit
     *
     * @since 0.5.2
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the unit
     *
     * @since 0.5.2
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * Check if the detail has a value
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasValue()
    {
        return $this->value !== null;
    }

    /**
     * Get the value
     *
     * @since 0.5.2
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value
     *
     * @since 0.5.2
     * @param Value $value
     */
    public function setValue(Value $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getKey()->isEqualTo($object->getKey()) &&
            $this->getType()->isEqualTo($object->getType()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            ($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
            ($this->hasValue() && $this->getValue()->isEqualTo($object->getValue()) || !$object->hasValue());
    }
}
