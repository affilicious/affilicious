<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Shop_Aware_Product_Interface as Shop_Aware;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Variant_Interface extends Product_Interface, Shop_Aware
{
    /**
     * @since 0.7
     * @param Complex_Product_Interface $parent
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Complex_Product_Interface $parent, Title $title, Name $name, Key $key);

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
