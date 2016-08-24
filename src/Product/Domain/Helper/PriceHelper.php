<?php
namespace Affilicious\Product\Domain\Helper;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class PriceHelper
{
    /**
     * Get the price with the correct currency.
     * If the value or currency is invalid, this functions returns false.
     *
     * @since 0.3
     * @param string|int $value
     * @param string $currency
     * @return string|null
     */
    public static function getPrice($value, $currency)
    {
        $currencySymbol = self::getCurrencySymbol($currency);
        if (empty($value) || empty($currencySymbol)) {
            return null;
        }

        $value = number_format($value, 2, '.', '');
        $price = $value . ' ' . $currencySymbol;

        return $price;
    }

    /**
     * Get the label for the currency option.
     *
     * @since 0.3
     * @param string $currency
     * @return bool|string
     */
    public static function getCurrencyLabel($currency)
    {
        if (!is_string($currency)) {
            return false;
        }

        $currencyLabel = ucwords($currency);
        $currencyLabel = strpos($currencyLabel, 'Us-') === 0 ? str_replace('Us-', 'US-', $currencyLabel) : $currencyLabel;
        $currencyLabel = __($currencyLabel, 'affilicious');

        return $currencyLabel;
    }

    /**
     * Get the symbol for the currency option
     *
     * @since 0.3
     * @param string $currency
     * @return string
     */
    public static function getCurrencySymbol($currency)
    {
        $currencies = array(
            'euro' => '€',
            'us-dollar' => '$',
        );

        $currencySymbol = isset($currencies[$currency]) ? $currencies[$currency] : '';

        return $currencySymbol;
    }
}
