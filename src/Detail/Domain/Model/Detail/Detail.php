<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail extends AbstractAggregate
{
    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var Unit
     */
    protected $unit;

    /**
     * @var Value
     */
    protected $value;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Type $type
     */
    public function __construct(Title $title, Name $name, Key $key, Type $type)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->type = $type;
    }

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get the type like text, number or file
     *
     * @since 0.6
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Check if the detail has an unit
     *
     * @since 0.6
     * @return bool
     */
    public function hasUnit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the unit
     *
     * @since 0.6
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the unit
     *
     * @since 0.6
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * Check if the detail has a value
     *
     * @since 0.6
     * @return bool
     */
    public function hasValue()
    {
        return $this->value !== null;
    }

    /**
     * Get the value
     *
     * @since 0.6
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value
     *
     * @since 0.6
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
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            $this->getKey()->isEqualTo($object->getKey()) &&
            $this->getType()->isEqualTo($object->getType()) &&
            ($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
            ($this->hasValue() && $this->getValue()->isEqualTo($object->getValue()) || !$object->hasValue());
    }
}
