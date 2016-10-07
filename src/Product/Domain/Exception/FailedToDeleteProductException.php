<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class FailedToDeleteProductException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ProductId|string|int $productVariantGroupId
     */
    public function __construct($productVariantGroupId)
    {
        parent::__construct(sprintf(
            "Failed to delete the product #%s",
            $productVariantGroupId
        ));
    }
}
