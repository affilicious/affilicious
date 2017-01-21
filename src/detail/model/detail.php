<?php
namespace Affilicious\Detail\Model;

use Affilicious\Common\Model\Name;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail
{
    /**
     * The name for display usage.
     *
     * @var Name
     */
    protected $name;

    /**
     * The concrete value for the attribute.
     *
     * @var Value
     */
    protected $value;

    /**
     * The type like text or numeric.
     *
     * @var Type
     */
    protected $type;

    /**
     * The optional unit like kg, cm or m².
     *
     * @var null|Unit
     */
    protected $unit;

    /**
     * Create a new text detail from the name and value.
     *
     * @since 0.8
     * @param Name $name
     * @param Value $value
     * @return Detail
     */
    public static function text(Name $name, Value $value)
    {
        return new self($name, $value, Type::text());
    }

    /**
     * Create a new number detail from the name, value and optional unit.
     *
     * @param Name $name
     * @param Value $value
     * @param Unit|null $unit
     * @return Detail
     */
    public static function number(Name $name, Value $value, Unit $unit = null)
    {
        return new self($name, $value, Type::number(), $unit);
    }

    /**
     * Create a new file detail from the name and value.
     *
     * @since 0.8
     * @param Name $name
     * @param Value $value
     * @return Detail
     */
    public static function file(Name $name, Value $value)
    {
        return new self($name, $value, Type::file());
    }

    /**
     * The unit will be stored only, if the type is number.
     *
     * @since 0.8
     * @param Name $name
     * @param Value $value
     * @param Type $type
     * @param null|Unit $unit
     */
	public function __construct(Name $name, Value $value, Type $type, Unit $unit = null)
	{
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->unit = $type->is_number() ? $unit : null;
    }

    /**
     * Get the name for display usage.
     *
     * @since 0.8
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Get the concrete value for the attribute.
     *
     * @since 0.8
     * @return Value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Get the type like text or numeric.
     *
     * @since 0.8
     * @return Type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Check if the optional unit exists.
     *
     * @since 0.8
     * @return bool
     */
    public function has_unit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like kg, cm or m².
     *
     * @since 0.8
     * @return null|Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     * Check if this detail is equal to the other one.
     *
     * @since 0.6
     * @param mixed $other
     * @return bool
     */
	public function is_equal_to($other)
	{
		return
			$other instanceof self &&
	        $this->get_name()->is_equal_to($other->get_name()) &&
            $this->get_value()->is_equal_to($other->get_value()) &&
	        $this->get_type()->is_equal_to($other->get_type()) &&
            ($this->has_unit() && $this->get_unit()->is_equal_to($other->get_unit()) || !$other->has_unit());
	}
}
