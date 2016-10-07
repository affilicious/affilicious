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
     * @param ProductId|string|int $productGroupId
     */
    public function __construct($productGroupId)
    {
        parent::__construct(sprintf(
            "The product #%s wasn't found.",
            $productGroupId
        ));
    }
}
