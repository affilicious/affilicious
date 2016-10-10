<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Shop\Shop;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedShopException extends DomainException
{
    /**
     * @since 0.6
     * @param Shop $detailTemplate
     * @param Product $product
     */
    public function __construct(Shop $detailTemplate, Product $product)
    {
        parent::__construct(sprintf(
            'The shop #%s (%s) does already exist in the product #%s (%s)',
            $detailTemplate->getId()->getValue(),
            $detailTemplate->getTitle()->getValue(),
            $product->getId()->getValue(),
            $product->getTitle()->getValue()
        ));
    }
}
