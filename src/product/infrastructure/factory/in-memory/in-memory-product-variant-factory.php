<?php
namespace Affilicious\Product\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Variant\Product_Variant;
use Affilicious\Product\Domain\Model\Variant\Product_Variant_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Product_Variant_Factory implements Product_Variant_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Product $parent_product, Title $title, Attribute_Group $attribute_group)
    {
        $name = $title->to_name();
        $product_variant = new Product_Variant(
            $parent_product,
            $title,
            $name,
            $name->to_key(),
            $attribute_group
        );

        return $product_variant;
    }
}
