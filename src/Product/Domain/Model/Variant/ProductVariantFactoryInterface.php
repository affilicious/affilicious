<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Product\Domain\Model\Product;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductVariantFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new shop template which can be stored into a database.
     *
     * @since 0.6
     * @param Product $parentProduct
     * @param Title $title
     * @param AttributeGroup $attributeGrou
     * @return ProductVariant
     */
    public function create(Product $parentProduct, Title $title, AttributeGroup $attributeGrou);
}
