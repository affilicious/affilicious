<?php
namespace Affilicious\Product\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Product\Domain\Model\Variant\ProductVariantFactoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryProductVariantFactory implements ProductVariantFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Product $parentProduct, Title $title)
    {
        $productVariant = new ProductVariant(
            $parentProduct,
            $title,
            $title->toName()
        );

        return $productVariant;
    }
}
