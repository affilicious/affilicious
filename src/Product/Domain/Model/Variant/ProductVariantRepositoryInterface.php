<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductId;
use Affilicious\Product\Domain\Model\ProductRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductVariantRepositoryInterface extends ProductRepositoryInterface
{
    /**
     * Store the product variant
     *
     * @since 0.6
     * @param Product $productVariant
     * @return ProductVariant
     */
    public function store(Product $productVariant);

    /**
     * Delete the product variant
     *
     * @since 0.6
     * @param ProductId $productVariantId
     * @return ProductVariant
     */
    public function delete(ProductId $productVariantId);

    /**
     * Find a product variant by the given ID
     *
     * @since 0.6
     * @param ProductId $productVariantId
     * @return null|ProductVariant
     */
    public function findById(ProductId $productVariantId);

    /**
     * Find all products variants
     *
     * @since 0.6
     * @return ProductVariant[]
     */
    public function findAll();
}
