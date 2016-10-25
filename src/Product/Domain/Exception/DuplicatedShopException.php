<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Shop\Domain\Model\Shop;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedShopException extends DomainException
{
    /**
     * @since 0.6
     * @param Shop $shop
     * @param Product $product
     */
    public function __construct(Shop $shop, Product $product)
    {
        parent::__construct(sprintf(
            'The shop #%s (%s) does already exist in the product #%s (%s)',
            $shop->getId()->getValue(),
            $shop->getTitle()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
