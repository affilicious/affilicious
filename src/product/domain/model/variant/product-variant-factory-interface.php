<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Product\Domain\Model\Product;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Variant_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new shop template which can be stored into a database.
     *
     * @since 0.6
     * @param Product $parent_product
     * @param Title $title
     * @param Attribute_Group $attribute_group
     * @return Product_Variant
     */
    public function create(Product $parent_product, Title $title, Attribute_Group $attribute_group);
}
