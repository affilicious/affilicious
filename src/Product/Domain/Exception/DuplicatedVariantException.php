<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedVariantException extends DomainException
{
    /**
     * @since 0.6
     * @param ProductVariant $productVariantVariant
     * @param Product $product
     */
    public function __construct(ProductVariant $productVariantVariant, Product $product)
    {
        parent::__construct(sprintf(
            'The variant #%s (%s) does already exist in the product #%s (%s)',
            $productVariantVariant->getId()->getValue(),
            $productVariantVariant->getTitle()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
