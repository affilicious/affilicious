<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Shop\Domain\Model\Currency;
use Affilicious\Shop\Domain\Model\Price;

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
