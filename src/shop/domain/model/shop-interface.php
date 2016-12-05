<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Aggregate_Interface;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Update_Aware_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Interface extends Aggregate_Interface, Update_Aware_Interface
{
    /**
     * @since 0.7
     * @param Shop_Template_Interface $template
     * @param Affiliate_Link $affiliate_link
     * @param Currency $currency
     */
    public function __construct(Shop_Template_Interface $template, Affiliate_Link $affiliate_link, Currency $currency);

    /**
     * Get the shop template.
     *
     * @since 0.7
     * @return Shop_Template
     */
    public function get_template();

    /**
     * Check if the shop has a thumbnail.
     *
     * @since 0.7
     * @return bool
     */
    public function has_thumbnail();

    /**
     * Get the optional thumbnail
     *
     * @since 0.7
     * @return null|Image
     */
    public function get_thumbnail();

    /**
     * Set the optional thumbnail
     *
     * @since 0.7
     * @param null|Image $thumbnail
     */
    public function set_thumbnail($thumbnail);

    /**
     * Get the affiliate link
     *
     * @since 0.7
     * @return Affiliate_Link
     */
    public function get_affiliate_link();

    /**
     * Check if the shop has an affiliate ID.
     *
     * @since 0.7
     * @return bool
     */
    public function has_affiliate_id();

    /**
     * Get the optional affiliate ID.
     *
     * @since 0.7
     * @return Affiliate_Id
     */
    public function get_affiliate_id();

    /**
     * Set the optional affiliate ID.
     *
     * @since 0.7
     * @param null|Affiliate_Id $affiliate_id
     */
    public function set_affiliate_id($affiliate_id);

    /**
     * Get the currency.
     *
     * @since 0.7
     * @return Currency
     */
    public function get_currency();

    /**
     * Get the availability like available or out of stock.
     *
     * @since 0.7
     * @return Availability
     */
    public function get_availability();

    /**
     * Set the availability like available or out of stock.
     *
     * @since 0.7
     * @param Availability $availability
     */
    public function set_availability(Availability $availability);

    /**
     * Check if the shop has a price.
     *
     * @since 0.7
     * @return bool
     */
    public function has_price();

    /**
     * Get the optional price.
     *
     * @since 0.7
     * @return null|Price
     */
    public function get_price();

    /**
     * Set the optional price.
     *
     * @since 0.7
     * @param null|Price $price
     */
    public function set_price($price);

    /**
     * Check if the shop has an old price.
     *
     * @since 0.7
     * @return bool
     */
    public function has_old_price();

    /**
     * Get the optional old price.
     *
     * @since 0.7
     * @return null|Price
     */
    public function get_old_price();

    /**
     * Set the optional old price.
     *
     * @since 0.7
     * @param null|Price $old_price
     */
    public function set_old_price($old_price);

    /**
     * Check if the shop has any delivery rates.
     *
     * @since 0.7
     * @return bool
     */
    public function has_delivery_rates();

    /**
     * Get the optional delivery rates.
     *
     * @since 0.7
     * @return null|Price
     */
    public function get_delivery_rates();

    /**
     * Set the optional delivery rates.
     *
     * @since 0.7
     * @param null|Price $delivery_rates
     */
    public function set_delivery_rates($delivery_rates);
}
