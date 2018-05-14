<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Money_Helper
{
    /**
     * Convert the money into an array.
     *
     * @since 0.9
     * @param Money $money
     * @return array
     */
    public static function to_array(Money $money)
    {
        $array = array(
            'value' => $money->get_value(),
            'currency' => array(
                'value' => $money->get_currency()->get_value(),
                'label' => $money->get_currency()->get_label(),
                'symbol' => $money->get_currency()->get_symbol(),
            )
        );

        $array = apply_filters('aff_money_to_array', $array, $money);

        return $array;
    }

    /**
     * Convert the money into a string.
     *
     * @since 0.9
     * @param Money $money
     * @return string
     */
    public static function to_string(Money $money)
    {
        $string = sprintf(
            '%s %s',
            $money->get_value(),
            $money->get_currency()->get_symbol()
        );

        $string = apply_filters('aff_money_to_string', $string, $money);

        return $string;
    }

    /**
     * Convert the array into money.
     *
     * @since 0.9
     * @param array $array
     * @return Money
     */
    public static function from_array(array $array)
    {
        $money = new Money(
            $array['value'],
            new Currency($array['currency']['value'])
        );

        $money = apply_filters('aff_array_to_money', $money, $array);

        return $money;
    }
}
