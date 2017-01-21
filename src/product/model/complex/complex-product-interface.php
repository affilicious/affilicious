<?php
namespace Affilicious\Product\Model\Complex;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Detail_Group_Aware_Product_Interface as Detail_Group_Aware;
use Affilicious\Product\Model\Image_Gallery_Aware_Product_Interface as Image_Gallery_Aware;
use Affilicious\Product\Model\Product_Interface;
use Affilicious\Product\Model\Relation_Aware_Product_Interface as Relation_Aware;
use Affilicious\Product\Model\Review_Aware_Product_Interface as Review_Aware;
use Affilicious\Product\Model\Variant\Product_Variant_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Complex_Product_Interface extends Product_Interface, Relation_Aware, Review_Aware, Image_Gallery_Aware, Detail_Group_Aware
{
    /**
     * Check if the product has a specific variant by the name.
     *
     * @since 0.7
     * @param Slug $name
     * @return bool
     */
    public function has_variant(Slug $name);

    /**
     * Add a new product variant.
     *
     * @since 0.7
     * @param Product_Variant_Interface $variant
     */
    public function add_variant(Product_Variant_Interface $variant);

    /**
     * Remove an existing product variant by the name.
     *
     * @since 0.7
     * @param Slug $name
     */
    public function remove_variant(Slug $name);

    /**
     * Get the product variant by the name.
     *
     * @since 0.7
     * @param Slug $name
     * @return null|Product_Variant_Interface
     */
    public function get_variant(Slug $name);

    /**
     * Get the default variant.
     *
     * @since 0.7
     * @return null|Product_Variant_Interface
     */
    public function get_default_variant();

    /**
     * Get all product variants.
     *
     * @since 0.7.1
     * @return Product_Variant_Interface[]
     */
    public function has_variants();

    /**
     * Get all product variants.
     *
     * @since 0.7
     * @return Product_Variant_Interface[]
     */
    public function get_variants();

    /**
     * Set all product variants.
     * If you do this, the old product variants going to be replaced.
     *
     * @since 0.7
     * @param Product_Variant_Interface[] $variants
     */
    public function set_variants($variants);
}
