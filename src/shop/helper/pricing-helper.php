<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Pricing;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Pricing_Helper
{
    /**
     * Convert the pricing into an array.
     *
     * @since 0.9
     * @param Pricing $pricing
     * @return array
     */
    public static function to_array(Pricing $pricing)
    {
        $array = array(
            'availability' => $pricing->get_availability()->get_value(),
            'price' => $pricing->has_price() ? Money_Helper::to_array($pricing->get_price()) : null,
            'old_price' => $pricing->has_price() ? Money_Helper::to_array($pricing->get_price()) : null,
        );

        $array = apply_filters('aff_pricing_to_array', $array, $pricing);

        return $array;
    }

    /**
     * Convert the array into a pricing.
     *
     * @since 0.9
     * @param array $array
     * @return Pricing
     */
    public static function from_array(array $array)
    {
        $pricing = new Pricing(
            new Availability($array['availability']),
            !empty($array['price']) ? Money_Helper::from_array($array['price']) : null,
            !empty($array['old_price']) ? Money_Helper::from_array($array['old_price']) : null
        );

        $pricing = apply_filters('aff_array_to_pricing', $pricing, $array);

        return $pricing;
    }
}
