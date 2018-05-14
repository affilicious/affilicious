<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Shop\Model\Price_Indication;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
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
        $array = array(
            'template_id' => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
            'name' => $shop->get_name()->get_value(),
            'slug' => $shop->get_slug()->get_value(),
            'updated_at' => $shop->get_updated_at()->getTimestamp(),
            'thumbnail' => $shop->has_thumbnail() ? Image_Helper::to_array($shop->get_thumbnail()) : null,
            'tracking' => Tracking_Helper::to_array($shop->get_tracking()),
            'pricing' => Pricing_Helper::to_array($shop->get_pricing()),
            'price_indication' => $shop->has_price_indication() ? $shop->get_price_indication()->get_value() : null,
            'custom_values' => $shop->has_custom_values() ? $shop->get_custom_values() : null,

	        // Deprecated 1.1. It's just used for legacy purpose. Use 'thumbnail' instead.
            'thumbnail_id' => $shop->has_thumbnail_id() ? $shop->get_thumbnail_id()->get_value() : null,
        );

        $array = apply_filters('aff_shop_to_array', $array, $shop);

        return $array;
    }

    /**
     * Convert the array into a shop.
     *
     * @since 0.9
     * @param array $array
     * @return Shop
     */
    public static function from_array(array $array)
    {
        $name = new Name($array['name']);
        $slug = new Slug($array['slug']);
        $tracking = Tracking_Helper::from_array($array['tracking']);
        $pricing = Pricing_Helper::from_array($array['pricing']);
        $shop = new Shop($name, $slug, $tracking, $pricing);

        if(!empty($array['template_id'])) {
            $shop->set_template_id(new Shop_Template_Id($array['template_id']));
        }

	    if(!empty($array['thumbnail']) && $thumbnail = Image_Helper::from_array($array['thumbnail'])) {
		    $shop->set_thumbnail($thumbnail);
	    }

        if(!empty($array['updated_at'])) {
            $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp($array['updated_at']));
        }

        if(!empty($array['custom_values'])) {
        	$shop->set_custom_values($array['custom_values']);
        }

	    if(!empty($array['price_indication'])) {
		    $shop->set_price_indication(new Price_Indication($array['price_indication']));
	    }

        $shop = apply_filters('aff_array_to_shop', $shop, $array);

        return $shop;
    }
}
