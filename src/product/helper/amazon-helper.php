<?php
namespace Affilicious\Product\Helper;

use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Pricing;
use Affilicious\Shop\Model\Tracking;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Helper
{
    /**
     * Find the tracking containing the affiliate link and product ID in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Tracking
     */
    public static function find_tracking(array $item)
    {
        $tracking = null;
        $affiliate_link = self::find_affiliate_link($item);
        $affiliate_product_id = self::find_affiliate_product_id($item);

        if($affiliate_link !== null) {
            $tracking = new Tracking($affiliate_link, $affiliate_product_id);
        }

        $tracking = apply_filters('aff_amazon_helper_find_tracking', $tracking, $item);

        return $tracking;
    }

    /**
     * Find the pricing containing the availability, price and old price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Pricing
     */
    public static function find_pricing(array $item)
    {
        $pricing = null;
        $availability = self::find_availability($item);
        $price = self::find_price($item);
        $old_price = self::find_old_price($item);

        if($availability !== null) {
            $pricing = new Pricing($availability, $price, $old_price);
        }

        $pricing = apply_filters('aff_amazon_helper_find_pricing', $pricing, $item);

        return $pricing;
    }

    /**
     * Find the affiliate link in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Affiliate_Link
     */
    public static function find_affiliate_link(array $item)
    {
        $affiliate_link = null;

        if(isset($item['DetailPageUrl'])) {
            $affiliate_link = new Affiliate_Link($item['DetailPageUrl']);
        }

        $affiliate_link = apply_filters('aff_amazon_helper_find_affiliate_link', $affiliate_link, $item);

        return $affiliate_link;
    }

    /**
     * Find the affiliate product ID in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Affiliate_Product_Id
     */
    public static function find_affiliate_product_id(array $item)
    {
        $affiliate_product_id = null;

        if(isset($item['ASIN'])) {
            $affiliate_product_id = new Affiliate_Product_Id($item['ASIN']);
        }

        $affiliate_product_id = apply_filters('aff_amazon_helper_find_affiliate_product_id', $affiliate_product_id, $item);

        return $affiliate_product_id;
    }

    /**
     * Find the availability in the Amazon API item response..
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Availability
     */
    public static function find_availability(array $item)
    {
        $availability = null;

        if(isset($item['Offers']['TotalOffers'])) {
            $total_offers = intval($item['Offers']['TotalOffers']);
            $availability = $total_offers > 0 ? Availability::available() : Availability::out_of_stock();
        }

        $availability = apply_filters('aff_amazon_helper_find_availability', $availability, $item);

        return $availability;
    }

    /**
     * Find the price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Money
     */
    public static function find_price(array $item)
    {
        $price = null;

        if(isset($item['Offers']['Offer']['OfferListing'])) {
            $offer_listing = $item['Offers']['Offer']['OfferListing'];
            $price = isset($offer_listing['SalePrice']) ? $offer_listing['SalePrice'] : $offer_listing['Price'];

            if(isset($price['Amount']) && isset($price['CurrencyCode'])) {
                $amount = floatval($price['Amount']) / 100;
                $currency = $price['CurrencyCode'];
                $price = new Money($amount, new Currency($currency));
            }
        }

        $price = apply_filters('aff_amazon_helper_find_price', $price, $item);

        return $price;
    }

    /**
     * Find the old price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Money
     */
    public static function find_old_price(array $item)
    {
        $old_price = null;

        if(isset($item['Offers']['Offer']['OfferListing'])) {
            $offerListing = $item['Offers']['Offer']['OfferListing'];
            $old_price = isset($offerListing['SalePrice']) && isset($offerListing['Price']) ? $offerListing['Price'] : null;

            if(isset($old_price['Amount']) && isset($old_price['CurrencyCode'])) {
                $amount = floatval($old_price['Amount']) / 100;
                $currency = $old_price['CurrencyCode'];
                $old_price = new Money($amount, new Currency($currency));
            }
        }

        $old_price = apply_filters('aff_amazon_helper_find_old_price', $old_price, $item);

        return $old_price;
    }
}
