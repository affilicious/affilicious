<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class MissingProductException extends DomainException
{
    /**
     * @since 0.6
     * @param ProductId $productVariantGroupId
     */
    public function __construct(ProductId $productVariantGroupId)
    {
        parent::__construct(sprintf(
            'The product #%s is missing in the database.',
            $productVariantGroupId
        ));
    }
}
