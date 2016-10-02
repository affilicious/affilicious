<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Shop\Currency;
use Affilicious\Product\Domain\Model\Shop\Price;

class InvalidPriceCurrencyException extends DomainException
{
    /**
     * @since 0.6
     * @param Price $price
     * @param Currency $currency
     */
    public function __construct(Price $price, Currency $currency)
    {
        parent::__construct(sprintf(
            'The price currency "%s" does not match "%s"',
            $price->getCurrency()->getValue(),
            $currency->getValue()
        ));
    }
}
