<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductVariantRepositoryInterface extends ProductRepositoryInterface
{
    /**
     * Find a product variant by the given ID
     *
     * @since 0.6
     * @param ProductId $productId
     * @return ProductVariant|null
     */
    public function findById(ProductId $productId);

    /**
     * Find all products variants
     *
     * @since 0.6
     * @return ProductVariant[]
     */
    public function findAll();
}
