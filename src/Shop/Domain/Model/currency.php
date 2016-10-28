<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Value_Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Currency extends Abstract_Value_Object
{
    const EURO = 'euro';
    const US_DOLLAR = 'us-dollar';

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
        $currency_label = ucwords($this->value);
        $currency_label = strpos($currency_label, 'us-') === 0 ? str_replace('us-', 'US-', $currency_label) : $currency_label;
        $currency_label = __($currency_label, 'affilicious');

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
            'euro' => '€',
            'us-dollar' => '$',
        );

        $currency_symbol = isset($currencies[$this->value]) ? $currencies[$this->value] : null;

        return $currency_symbol;
    }
}
