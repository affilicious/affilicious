<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ParentProductNotFoundException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ProductId|string|int $productId
     * @param ProductId|string|int $productVariantId
     */
    public function __construct($productId, $productVariantId)
    {
        parent::__construct(sprintf(
            "The parent product #%s for the variant #%s wasn't found.",
            $productId,
            $productVariantId
        ));
    }
}
