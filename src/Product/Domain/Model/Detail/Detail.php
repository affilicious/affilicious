<?php
namespace Affilicious\Product\Domain\Model\Detail;

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
    private $title;

    /**
     * @var Key
     */
    private $key;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var Unit
     */
    private $unit;

    /**
     * @var Value
     */
    private $value;

    /**
     * @since 0.6
     * @param Title $title
     * @param Key $key
     * @param Type $type
     */
    public function __construct(Title $title, Key $key, Type $type)
    {
        $this->title = $title;
        $this->key = $key;
        $this->type = $type;
    }

    /**
     * Get the title
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
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
            $this->getKey()->isEqualTo($object->getKey()) &&
            $this->getType()->isEqualTo($object->getType()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            ($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
            ($this->hasValue() && $this->getValue()->isEqualTo($object->getValue()) || !$object->hasValue());
    }
}
