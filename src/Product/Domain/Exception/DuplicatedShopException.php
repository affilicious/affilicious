<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Shop\Shop;

class DuplicatedShopException extends DomainException
{
    /**
     * @since 0.5.2
     * @param Shop $detail
     * @param Product $product
     */
    public function __construct(Shop $detail, Product $product)
    {
        parent::__construct(sprintf(
            'The shop #%s (%s) does already exist in the product #%s (%s)',
            $detail->getId()->getValue(),
            $detail->getTitle()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
