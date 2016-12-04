<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Attribute\Domain\Exception\Duplicated_Attribute_Exception;
use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Aggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Group extends Abstract_Aggregate
{
    /**
     * This ID is the same as the related template
     *
     * @var Attribute_Template_Group_Id
     */
    protected $template_id;

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
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->attributes = array();
    }

    /**
     * Check if the attribute group has a template ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_template_id()
    {
        return $this->template_id !== null;
    }

    /**
     * Get the attribute group template ID
     *
     * @since 0.6
     * @return null|Attribute_Template_Group_Id
     */
    public function get_template_id()
    {
        return $this->template_id;
    }

    /**
     * Set the attribute group template ID
     *
     * @since 0.6
     * @param null|Attribute_Template_Group_Id $template_id
     * @throws Invalid_Type_Exception
     */
    public function set_template_id($template_id)
    {
        if($template_id !== null && !($template_id instanceof Attribute_Template_Group_Id)) {
            throw new Invalid_Type_Exception($template_id, Attribute_Template_Group_Id::class);
        }

        $this->template_id = $template_id;
    }

    /**
     * Get the title
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
     * @return Key
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * Check if a attribute with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function has_attribute(Name $name)
    {
        return isset($this->attributes[$name->get_value()]);
    }

    /**
     * Add a new attribute
     *
     * @since 0.6
     * @param Attribute $attribute
     * @throws Duplicated_Attribute_Exception
     */
    public function add_attribute(Attribute $attribute)
    {
        /*
        if($this->has_attribute($attribute->get_name())) {
            throw new Duplicated_Attribute_Exception($attribute, $this);
        }
        */

        $this->attributes[$attribute->get_name()->get_value()] = $attribute;
    }

    /**
     * Remove an existing attribute by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function remove_attribute(Name $name)
    {
        unset($this->attributes[$name->get_value()]);
    }

    /**
     * Get an existing attribute by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|Attribute
     */
    public function get_attribute(Name $name)
    {
        if($this->has_attribute($name)) {
            return $this->attributes[$name->get_value()];
        }

        return null;
    }

    /**
     * Get all attributes
     *
     * @since 0.6
     * @return Attribute[]
     */
    public function get_attributes()
    {
        $attributes = array_values($this->attributes);

        return $attributes;
    }

    /**
     * Set all attributes
     *
     * @since 0.6
     * @param Attribute[] $attributes
     */
    public function set_attributes($attributes)
    {
        $this->attributes = array();

        // add_attribute checks for the type
        foreach ($attributes as $attribute) {
            $this->add_attribute($attribute);
        }
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            ($this->has_template_id() && $this->get_template_id()->is_equal_to($object->get_template_id()) || !$object->has_template_id()) &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key());
            // TODO: A good way to compare two arrays with objects
    }
}
