<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\ProductId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class MissingParentProductException extends DomainException
{
    /**
     * @since 0.6
     * @param ProductId $productVariantId
     */
    public function __construct(ProductId $productVariantId)
    {
        parent::__construct(sprintf(
            'The parent product for the variant #%s is missing in the database.',
            $productVariantId
        ));
    }
}
