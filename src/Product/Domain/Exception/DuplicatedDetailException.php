<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Detail\Detail;

class DuplicatedDetailException extends DomainException
{
    /**
     * @since 0.5.2
     * @param Detail $detail
     * @param Product $product
     */
    public function __construct(Detail $detail, Product $product)
    {
        parent::__construct(sprintf(
            'The detail %s (%s) does already exist in the product #%s (%s)',
            $detail->getKey()->getValue(),
            $detail->getName()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
