<?php
namespace Affilicious\Shop\Helper;

use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Tracking;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Tracking_Helper
{
    /**
     * Convert the tracking into an array.
     *
     * @since 0.9
     * @param Tracking $tracking
     * @return array
     */
    public static function to_array(Tracking $tracking)
    {
        $array = array(
            'affiliate_link' => $tracking->get_affiliate_link()->get_value(),
            'affiliate_product_id' => $tracking->has_affiliate_product_id() ? $tracking->get_affiliate_product_id()->get_value() : null,

            // Deprecated 1.0. It's just used for legacy purpose. Use 'affiliate_product_id' instead.
            'affiliate_id' => $tracking->has_affiliate_id() ? $tracking->get_affiliate_id()->get_value() : null,
        );

        $array = apply_filters('aff_tracking_to_array', $array, $tracking);

        return $array;
    }

    /**
     * Convert the array into a tracking.
     *
     * @since 0.9
     * @param array $array
     * @return Tracking
     */
    public static function from_array(array $array)
    {
        $affiliate_product_id = null;
        if(!empty($array['affiliate_product_id'])) {
            $affiliate_product_id = $array['affiliate_product_id'];
        }

        // Deprecated 1.0. It's just used for legacy purpose. Use 'affiliate_product_id' instead.
        if(!empty($array['affiliate_product_id'])) {
            $affiliate_product_id = $array['affiliate_product_id'];
        }

        $tracking = new Tracking(
            new Affiliate_Link($array['affiliate_link']),
            !empty($affiliate_product_id) ? new Affiliate_Product_Id($affiliate_product_id) : null
        );

        $tracking = apply_filters('aff_array_to_tracking', $tracking, $array);

        return $tracking;
    }
}
