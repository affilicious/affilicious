<?php
namespace Affilicious\Detail\Model;

use Affilicious\Common\Model\Custom_Value_Aware_Trait;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Detail
{
    use Name_Aware_Trait, Slug_Aware_Trait, Type_Trait, Unit_Trait, Custom_Value_Aware_Trait {
        Name_Aware_Trait::set_name as private;
        Slug_Aware_Trait::set_slug as private;
        Type_Trait::set_type as private;
        Unit_Trait::set_unit as private;
    }

    /**
     * The concrete value for the attribute.
     *
     * @since 0.8
     * @var Value
     */
    protected $value;

    /**
     * The optional detail template ID.
     *
     * @since 0.8
     * @var Detail_Template_Id
     */
    protected $template_id;

    /**
     * Create a new text detail from the name and value.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Value $value
     * @return Detail
     */
    public static function text(Name $name, Slug $slug, Value $value)
    {
        return new self($name, $slug, $value, Type::text());
    }

    /**
     * Create a new number detail from the name, value and optional unit.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Value $value
     * @param Unit|null $unit
     * @return Detail
     */
    public static function number(Name $name, Slug $slug, Value $value, Unit $unit = null)
    {
        $detail = new self($name, $slug, $value, Type::number(), $unit);

        return $detail;
    }

    /**
     * Create a new boolean detail from the name and value.
     *
     * @since 0.8.9
     * @param Name $name
     * @param Slug $slug
     * @param Value $value
     * @return Detail
     */
    public static function boolean(Name $name, Slug $slug, Value $value)
    {
        $detail = new self($name, $slug, $value, Type::boolean());

        return $detail;
    }

    /**
     * Create a new file detail from the name and value.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Value $value
     * @return Detail
     */
    public static function file(Name $name, Slug $slug, Value $value)
    {
        return new self($name, $slug, $value, Type::file());
    }

    /**
     * The unit will be stored only, if the type is number.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Value $value
     * @param Type $type
     * @param Unit $unit
     */
	public function __construct(Name $name, Slug $slug, Value $value, Type $type, Unit $unit = null)
	{
        $this->set_name($name);
        $this->set_slug($slug);
        $this->value = $value;
        $this->standardize($type, $unit);
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
     * Standardize the detail with the type and optional unit.
     * The unit will be stored only, if the type is number.
     *
     * @since 0.8
     * @param Type $type The type like text or numeric
     * @param null|Unit $unit The optional unit like kg, cm or m².
     */
    public function standardize(Type $type, Unit $unit = null)
    {
        $this->set_type($type);
        $this->set_unit($type->is_number() ? $unit : null);
    }

    /**
     * Check if the detail has an optional template ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_template_id()
    {
        return $this->template_id !== null;
    }

    /**
     * Get the optional detail template ID.
     *
     * @since 0.8
     * @return null|Detail_Template_Id
     */
    public function get_template_id()
    {
        return $this->template_id;
    }

    /**
     * Set the optional detail template ID.
     *
     * @since 0.8
     * @param null|Detail_Template_Id $template_id
     */
    public function set_template_id(Detail_Template_Id $template_id = null)
    {
        $this->template_id = $template_id;
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
	        $this->get_slug()->is_equal_to($other->get_slug()) &&
            $this->get_value()->is_equal_to($other->get_value()) &&
	        $this->get_type()->is_equal_to($other->get_type()) &&
            ($this->has_unit() && $this->get_unit()->is_equal_to($other->get_unit()) || !$other->has_unit()) &&
            ($this->has_template_id() && $this->get_template_id()->is_equal_to($other->get_template_id()) || !$other->has_template_id());
	}
}
