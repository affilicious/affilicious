<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\Domain_Exception;
use Affilicious\Shop\Domain\Model\Currency;
use Affilicious\Shop\Domain\Model\Price;

class Invalid_Price_Currency_Exception extends Domain_Exception
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
            $price->get_currency()->get_value(),
            $currency->get_value()
        ));
    }
}
