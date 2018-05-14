<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Complex_Product extends Product implements Excerpt_Aware_Interface, Content_Aware_Interface, Detail_Aware_Interface, Relation_Aware_Interface, Review_Aware_Interface
{
    use Excerpt_Aware_Trait, Content_Aware_Trait, Detail_Aware_Trait, Relation_Aware_Trait, Review_Aware_Trait {
        Detail_Aware_Trait::__construct as private init_details;
        Relation_Aware_Trait::__construct as private init_relations;
    }

    /**
     * Holds all product variants of the complex product.
     *
     * @since 0.8
     * @var Product_Variant[]
     */
	protected $variants;

    /**
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     */
    public function __construct(Name $name, Slug $slug)
    {
        parent::__construct($name, $slug, Type::complex());
        $this->variants = array();
        $this->init_details();
        $this->init_relations();
    }

    /**
     * Check if the product has a specific variant by the name.
     *
     * @since 0.8
     * @param Slug $slug
     * @return bool
     */
    public function has_variant(Slug $slug)
    {
        return isset($this->variants[$slug->get_value()]);
    }

    /**
     * Add a new product variant.
     *
     * @since 0.8
     * @param Product_Variant $variant
     */
    public function add_variant(Product_Variant $variant)
    {
        $this->variants[$variant->get_slug()->get_value()] = $variant;
    }

    /**
     * Remove an existing product variant by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     */
    public function remove_variant(Slug $slug)
    {
        unset($this->variants[$slug->get_value()]);
    }

    /**
     * Check if the product has any variants.
     * @since 0.8
     * @return bool
     */
    public function has_variants()
    {
        return !empty($this->variants);
    }

    /**
     * Get the product variant by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Product_Variant
     */
    public function get_variant(Slug $slug)
    {
        if(!$this->has_variant($slug)) {
            return null;
        }

        $variant = $this->variants[$slug->get_value()];

        return $variant;
    }

    /**
     * Get the default product variant.
     *
     * @since 0.8
     * @return null|Product_Variant
     */
    public function get_default_variant()
    {
        foreach ($this->variants as $variant) {
            if($variant->is_default()) {
                return $variant;
            }
        }

        // If there is no default, just take out the first one
        if(count($this->variants) > 0) {
            return reset($this->variants);
        }

        return null;
    }

    /**
     * Get all product variants.
     *
     * @since 0.8
     * @return Product_Variant[]
     */
    public function get_variants()
    {
        $variants = array_values($this->variants);

        return $variants;
    }

    /**
     * Set all product variants.
     * If you do this, the old product variants going to be replaced.
     *
     * @since 0.8
     * @param Product_Variant[] $variants
     */
    public function set_variants($variants)
    {
    	Assert_Helper::all_is_instance_of($variants, Product_Variant::class, __METHOD__, 'Expected an array of variants. But one of the values is %s', '0.9.2');

        $this->variants = $variants;
    }

    /**
     * Check if this complex product is equal to the other one.
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
            $this->get_variants() == $other->has_variants() &&
            ($this->has_excerpt() && $this->get_excerpt()->is_equal_to($other->get_excerpt()) || !$other->has_excerpt()) &&
            ($this->has_content() && $this->get_content()->is_equal_to($other->get_content()) || !$other->has_content()) &&
            $this->get_details() == $other->get_details() &&
            $this->get_related_products() == $other->get_related_products() &&
            $this->get_related_accessories() == $other->get_related_accessories() &&
            ($this->has_review() && $this->get_review()->is_equal_to($other->get_review()) || !$other->has_review());
    }
}
