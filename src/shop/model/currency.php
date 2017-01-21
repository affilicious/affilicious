<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Currency
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    const EURO = 'EUR';
    const US_DOLLAR = 'USD';

    /**
     * Get an Euro currency.
     *
     * @since 0.8
     * @return Currency
     */
    public static function euro()
    {
        return new self(self::EURO);
    }

    /**
     * Get a US-Dollar currency.
     *
     * @since 0.8
     * @return Currency
     */
    public static function us_dollar()
    {
        return new self(self::US_DOLLAR);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct($value)
    {
        $values = apply_filters('affilicious_shop_currency_values', array(
            self::EURO,
            self::US_DOLLAR
        ));

        Assert::stringNotEmpty($value, 'The currency must be a non empty string. Got: %s');
        Assert::oneOf($value, $values, 'Expected currency of: %2$s. Got: %s');

        $this->set_value($value);
    }

    /**
     * Get the translated label for the currency
     *
     * @since 0.8
     * @return string
     */
    public function get_label()
    {
        $labels = apply_filters('affilicious_shop_currency_labels', array(
            self::EURO => __('Euro', 'affilicous'),
            self::US_DOLLAR => __('US-Dollar', 'affilicous'),
        ));

        $currency_label = isset($labels[$this->value]) ? $labels[$this->value] : null;

        return $currency_label;
    }

    /**
     * Get the symbol for the currency
     *
     * @since 0.8
     * @return null|string
     */
    public function get_symbol()
    {
        $currencies = apply_filters('affilicious_shop_currency_symbols', array(
            self::EURO => '€',
            self::US_DOLLAR => '$',
        ));

        $currency_symbol = isset($currencies[$this->value]) ? $currencies[$this->value] : null;

        return $currency_symbol;
    }
}
