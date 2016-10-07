<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariantNotFoundException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ProductId|string|int $productVariantGroupId
     */
    public function __construct($productVariantGroupId)
    {
        parent::__construct(sprintf(
            "The product variant #%s wasn't found.",
            $productVariantGroupId
        ));
    }
}
