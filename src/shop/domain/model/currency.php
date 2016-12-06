<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Value_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Currency extends Abstract_Value_Object
{
    /**
     * @deprecated
     */
    const LEGACY_EURO = 'euro';

    /**
     * @deprecated
     */
    const LEGACY_US_DOLLAR = 'us-dollar';

    const EURO = 'EUR';
    const US_DOLLAR = 'USD';

    /**
     * Get a Euro currency
     *
     * @since 0.6
     * @return Currency
     */
    public static function get_euro()
    {
        return new self(self::EURO);
    }

    /**
     * Get a US-Dollar currency
     *
     * @since 0.6
     * @return Currency
     */
    public static function get_us_dollar()
    {
        return new self(self::US_DOLLAR);
    }

    /**
     * @inheritdoc
     */
    public function __construct($value)
    {
        if($value === self::LEGACY_EURO) {
            $value = self::EURO;
        }

        if($value === self::LEGACY_US_DOLLAR) {
            $value = self::US_DOLLAR;
        }

        $currencies = array(
            self::EURO,
            self::US_DOLLAR,
        );

        if(!in_array($value, $currencies)) {
            throw new Invalid_Value_Exception($value, $currencies, get_class($this));
        }

        parent::__construct($value);
    }

    /**
     * Get the translated label for the currency
     *
     * @since 0.6
     * @return string
     */
    public function get_label()
    {
        $labels = array(
            self::EURO => 'Euro',
            self::US_DOLLAR => 'US-Dollar',
        );

        $currency_label = isset($labels[$this->value]) ? $labels[$this->value] : null;
        $currency_label = __($currency_label, 'affilicous');

        return $currency_label;
    }

    /**
     * Get the symbol for the currency
     *
     * @since 0.6
     * @return null|string
     */
    public function get_symbol()
    {
        $currencies = array(
            self::EURO => '€',
            self::US_DOLLAR => '$',
        );

        $currency_symbol = isset($currencies[$this->value]) ? $currencies[$this->value] : null;

        return $currency_symbol;
    }
}
