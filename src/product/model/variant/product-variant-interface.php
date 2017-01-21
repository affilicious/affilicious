<?php
namespace Affilicious\Product\Model\Variant;

use Affilicious\Attribute\Model_Group;
use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Model\Product_Interface;
use Affilicious\Product\Model\Shop_Aware_Product_Interface as Shop_Aware;
use Affilicious\Product\Model\Tag_Aware_Product_Interface as Tag_Aware;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Variant_Interface extends Product_Interface, Shop_Aware, Tag_Aware
{
    /**
     * @since 0.7
     * @param Complex_Product_Interface $parent
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     */
    public function __construct(Complex_Product_Interface $parent, Name $title, Slug $name, Key $key);

    /**
     * Get the parent complex product.
     *
     * @since 0.7
     * @return Complex_Product_Interface
     */
    public function get_parent();

    /**
     * Set or unset the product variant as the default for the parent complex product.
     *
     * @since 0.7
     * @param bool $default
     */
    public function set_default($default);

    /**
     * Check if the product variant is the default one for the parent complex product.
     *
     * @since 0.7
     * @return bool
     */
    public function is_default();

    /**
     * Check if the product variants has an attribute group.
     *
     * @since 0.7
     * @return bool
     */
    public function has_attribute_group();

    /**
     * Get the attribute group which stores the attributes like color or size.
     *
     * @since 0.7
     * @return Attribute_Group
     */
    public function get_attribute_group();

    /**
     * Set the attribute group which stores the attributes like color or size.
     *
     * @since 0.7
     * @param Attribute_Group $attribute_group
     */
    public function set_attribute_group(Attribute_Group $attribute_group);
}
