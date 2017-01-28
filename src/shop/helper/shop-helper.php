<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Common\Helper\Time_Helper;
use Affilicious\Shop\Model\Shop;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop_Helper
{
    /**
     * Convert the shop into an array.
     *
     * @since 0.8
     * @param Shop $shop
     * @return array
     */
    public static function to_array(Shop $shop)
    {
        $raw_shop = array(
            'template_id' => $shop->get_template_id(),
            'name' => $shop->get_name()->get_value(),
            'slug' => $shop->get_slug(),
            'updated_at' => Time_Helper::get_datetime_i18n($shop->get_updated_at()->getTimestamp()),
            'thumbnail_id' => $shop->has_thumbnail_id() ? $shop->get_thumbnail_id()->get_value() : null,
            'tracking' => array(
                'affiliate_link' => $shop->get_tracking()->get_affiliate_link(),
                'affiliate_id' => $shop->get_tracking()->has_affiliate_id() ? $shop->get_tracking()->get_affiliate_id()->get_value() : null,
            ),
            'pricing' => array(
                'availability' => $shop->get_pricing()->get_availability()->get_value(),
                'price' => !$shop->get_pricing()->has_price() ? null : array(
                    'value' => $shop->get_pricing()->get_price()->get_value(),
                    'currency' => array(
                        'value' => $shop->get_pricing()->get_price()->get_currency()->get_value(),
                        'label' => $shop->get_pricing()->get_price()->get_currency()->get_label(),
                        'symbol' => $shop->get_pricing()->get_price()->get_currency()->get_symbol(),
                    ),
                ),
                'old_price' => !$shop->get_pricing()->has_old_price() ? null : array(
                    'value' => $shop->get_pricing()->get_old_price()->get_value(),
                    'currency' => array(
                        'value' => $shop->get_pricing()->get_old_price()->get_currency()->get_value(),
                        'label' => $shop->get_pricing()->get_old_price()->get_currency()->get_label(),
                        'symbol' => $shop->get_pricing()->get_old_price()->get_currency()->get_symbol(),
                    ),
                ),
            )
        );

        return $raw_shop;
    }
}
