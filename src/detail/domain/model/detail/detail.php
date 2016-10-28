<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\Abstract_Aggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail extends Abstract_Aggregate
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
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * Get the type like text, number or file
     *
     * @since 0.6
     * @return Type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Check if the detail has an unit
     *
     * @since 0.6
     * @return bool
     */
    public function has_unit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the unit
     *
     * @since 0.6
     * @return Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     * Set the unit
     *
     * @since 0.6
     * @param Unit $unit
     */
    public function set_unit(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * Check if the detail has a value
     *
     * @since 0.6
     * @return bool
     */
    public function has_value()
    {
        return $this->value !== null;
    }

    /**
     * Get the value
     *
     * @since 0.6
     * @return Value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Set the value
     *
     * @since 0.6
     * @param Value $value
     */
    public function set_value(Value $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key()) &&
            $this->get_type()->is_equal_to($object->get_type()) &&
            ($this->has_unit() && $this->get_unit()->is_equal_to($object->get_unit()) || !$object->has_unit()) &&
            ($this->has_value() && $this->get_value()->is_equal_to($object->get_value()) || !$object->has_value());
    }
}
