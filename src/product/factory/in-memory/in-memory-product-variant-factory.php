<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Factory\Product_Variant_Factory_Interface;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product_Variant;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Product_Variant_Factory implements Product_Variant_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create(Complex_Product $parent, Name $name, Slug $slug)
    {
        do_action('aff_product_variant_factory_before_create');
        do_action('aff_product_factory_before_create');

        $product_variant = new Product_Variant($parent, $name, $slug);
        $product_variant = apply_filters('aff_product_variant_factory_create', $product_variant);
        $product_variant = apply_filters('aff_product_factory_create', $product_variant);

        do_action('aff_product_variant_factory_after_create');
        do_action('aff_product_factory_after_create');

        return $product_variant;
    }
}
