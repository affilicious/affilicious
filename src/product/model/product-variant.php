<?php
namespace Affilicious\Product\Model;

use Affilicious\Attribute\Model\Attribute;
use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Variant extends Product implements Shop_Aware_Interface, Tag_Aware_Interface
{
    use Tag_Aware_Trait, Shop_Aware_Trait {
        Tag_Aware_Trait::__construct as private init_tags;
        Shop_Aware_Trait::__construct as private init_shops;
    }

    /**
     * @var Complex_Product
     */
    protected $parent;

    /**
     * Indicates if the variant is the default one for the parent complex product.
     *
     * @var bool
     */
    protected $default;

    /**
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @since 0.8
     * @param Complex_Product $parent
     * @param Name $title
     * @param Slug $name
     */
    public function __construct(Complex_Product $parent, Name $title, Slug $name)
    {
        parent::__construct($title, $name, Type::variant());
        $this->parent = $parent;
        $this->default = false;
        $this->attributes = array();
        $this->init_tags();
        $this->init_shops();
    }

    /**
     * Get the parent complex product.
     *
     * @since 0.8
     * @return Complex_Product
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * Set or unset the product variant as the default for the parent complex product.
     *
     * @since 0.8
     * @param bool $default
     */
    public function set_default($default)
    {
    	Assert_Helper::is_boolean($default, __METHOD__, 'Expected default to be a boolean. Got: %s', '0.9.2');

        $this->default = $default;
    }

    /**
     * Check if the product variant is the default one for the parent complex product.
     *
     * @since 0.8
     * @return bool
     */
    public function is_default()
    {
        return $this->default;
    }

    /**
     * Check if the product has a specific attribute by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return bool
     */
    public function has_attribute(Slug $slug)
    {
        return isset($this->attributes[$slug->get_value()]);
    }

    /**
     * Add a new product attribute.
     *
     * @since 0.8
     * @param Attribute $attribute
     */
    public function add_attribute(Attribute $attribute)
    {
        $this->attributes[$attribute->get_slug()->get_value()] = $attribute;
    }

    /**
     * Remove the product attribute by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     */
    public function remove_attribute(Slug $slug)
    {
        unset($this->attributes[$slug->get_value()]);
    }

    /**
     * Get the product attribute by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Attribute
     */
    public function get_attribute(Slug $slug)
    {
        if(!$this->has_attribute($slug)) {
            return null;
        }

        $attribute = $this->attributes[$slug->get_value()];

        return $attribute;
    }

    /**
     * Check if the product has any attributes.
     *
     * @since 0.8
     * @return bool
     */
    public function has_attributes()
    {
        return !empty($this->attributes);
    }

    /**
     * Get the product attributes.
     *
     * @since 0.8
     * @return Attribute[]
     */
    public function get_attributes()
    {
        $attributes = array_values($this->attributes);

        return $attributes;
    }

    /**
     * Set the product attributes.
     * If you do this, the old attributes going to be replaced.
     *
     * @since 0.8
     * @param Attribute[] $attributes
     */
    public function set_attributes($attributes)
    {
    	Assert_Helper::all_is_instance_of($attributes, Attribute::class, __METHOD__, 'Expected an array of attributes. But one of the values is %s', '0.9.2');

        $this->attributes = $attributes;
    }

    /**
     * Check if this product variant is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            parent::is_equal_to($other) &&
            $this->is_default() == $other->is_default() &&
            $this->get_tags() == $other->get_tags() &&
            $this->get_shops() == $other->get_shops();
    }
}
