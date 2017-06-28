<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Shop\Model\Money;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

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
        $result = array(
            'value' => $money->get_value(),
            'currency' => $money->get_currency()->get_value(),
        );

        return $result;
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
        $result = sprintf(
            '%s %s',
            $money->get_value(),
            $money->get_currency()->get_symbol()
        );

        return $result;
    }
}
