<?php
namespace Affilicious\Product\Domain\Model\Complex;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Product\Domain\Model\Variant\Product_Variant_Interface;
use Affilicious\Product\Domain\Model\Detail_Group_Aware_Product_Interface as Detail_Group_Aware;
use Affilicious\Product\Domain\Model\Image_Gallery_Aware_Product_Interface as Image_Gallery_Aware;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Relation_Aware_Product_Interface as Relation_Aware;
use Affilicious\Product\Domain\Model\Review_Aware_Product_Interface as Review_Aware;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Complex_Product_Interface extends Product_Interface, Relation_Aware, Review_Aware, Image_Gallery_Aware, Detail_Group_Aware
{
    /**
     * Check if the product has a specific variant by the name.
     *
     * @since 0.7
     * @param Name $name
     * @return bool
     */
    public function has_variant(Name $name);

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
     * @param Name $name
     */
    public function remove_variant(Name $name);

    /**
     * Get the product variant by the name.
     *
     * @since 0.7
     * @param Name $name
     * @return null|Product_Variant_Interface
     */
    public function get_variant(Name $name);

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
