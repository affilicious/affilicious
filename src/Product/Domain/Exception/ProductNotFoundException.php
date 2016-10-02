<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductNotFoundException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ProductId|string|int $productId
     */
    public function __construct($productId)
    {
        parent::__construct(sprintf(
            "The product #%s wasn't found.",
            $productId
        ));
    }
}
