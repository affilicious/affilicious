<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidValueException;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Currency extends AbstractValueObject
{
    const EURO = 'euro';
    const US_DOLLAR = 'us-dollar';

    /**
     * Get a Euro currency
     *
     * @since 0.6
     * @return Currency
     */
    public static function getEuro()
    {
        return new self(self::EURO);
    }

    /**
     * Get a US-Dollar currency
     *
     * @since 0.6
     * @return Currency
     */
    public static function getUsDollar()
    {
        return new self(self::US_DOLLAR);
    }

    /**
     * @inheritdoc
     */
    public function __construct($value)
    {
        $currencies = array(
            self::EURO,
            self::US_DOLLAR,
        );

        if(!in_array($value, $currencies)) {
            throw new InvalidValueException($value, $currencies, get_class($this));
        }

        parent::__construct($value);
    }

    /**
     * Get the translated label for the currency
     *
     * @since 0.6
     * @return string
     */
    public function getLabel()
    {
        $currencyLabel = ucwords($this->value);
        $currencyLabel = strpos($currencyLabel, 'Us-') === 0 ? str_replace('Us-', 'US-', $currencyLabel) : $currencyLabel;
        $currencyLabel = __($currencyLabel, 'affilicious');

        return $currencyLabel;
    }

    /**
     * Get the symbol for the currency
     *
     * @since 0.6
     * @return null|string
     */
    public function getSymbol()
    {
        $currencies = array(
            'euro' => '€',
            'us-dollar' => '$',
        );

        $currencySymbol = isset($currencies[$this->value]) ? $currencies[$this->value] : null;

        return $currencySymbol;
    }
}
