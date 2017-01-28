<?php
namespace Affilicious\Attribute\Model;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template
{
    use Name_Aware_Trait, Slug_Aware_Trait, Type_Trait, Unit_Trait {
        Type_Trait::set_type as private;
        Unit_Trait::set_unit as private;
    }

    /**
     * There is a limit of 20 characters for taxonomies in Wordpress.
     */
    const TAXONOMY = 'aff_attribute_tmpl';

    /**
     * The optional and unique ID of the attribute template.
     *
     * @var null|Attribute_Template_Id
     */
    private $id;

    /**
     * The unit will be stored only, if the type is number.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Type $type
     * @param null|Unit $unit
     */
	public function __construct(Name $name, Slug $slug, Type $type, Unit $unit = null)
	{
        $this->set_name($name);
        $this->set_slug($slug);
        $this->standardize($type, $unit);
    }

    /**
     * Check if the attribute template has an unique ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the unique ID of the attribute template.
     *
     * @since 0.8
     * @return null|Attribute_Template_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the unique ID of the attribute template.
     *
     * @since 0.8
     * @param null|Attribute_Template_Id $id
     */
    public function set_id(Attribute_Template_Id $id = null)
    {
        $this->id = $id;
    }

    /**
     * Standardize the attribute template with the type and optional unit.
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
     * Build a new attribute from the template.
     *
     * @since 0.8
     * @param Value $value
     * @return Attribute
     */
    public function build(Value $value)
    {
        $attribute = new Attribute($this->name, $this->slug, $value, $this->type, $this->unit);

        return $attribute;
    }

    /**
     * Check if this attribute template is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
	public function is_equal_to($other)
	{
		return
			$other instanceof self &&
            ($this->has_id() && $this->get_id()->is_equal_to($other->get_id()) || !$other->has_id()) &&
	        $this->get_name()->is_equal_to($other->get_name()) &&
	        $this->get_slug()->is_equal_to($other->get_slug()) &&
	        $this->get_type()->is_equal_to($other->get_type()) &&
            ($this->has_unit() && $this->get_unit()->is_equal_to($other->get_unit()) || !$other->has_unit());
	}
}
