<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class FailedToDeleteProductVariantException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ProductId|string|int $productVariantId
     */
    public function __construct($productVariantId)
    {
        parent::__construct(sprintf(
            'Failed to delete the product variant #%s',
            $productVariantId
        ));
    }
}
