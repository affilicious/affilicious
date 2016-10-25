<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Product\Domain\Model\Product;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedDetailGroupException extends DomainException
{
    /**
     * @since 0.6
     * @param DetailGroup $detailGroup
     * @param Product $product
     */
    public function __construct(DetailGroup $detailGroup, Product $product)
    {
        parent::__construct(sprintf(
            'The detail group %s (%s) does already exist in the product #%s (%s)',
            $detailGroup->getName()->getValue(),
            $detailGroup->getTitle()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
