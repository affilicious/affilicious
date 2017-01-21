<?php
namespace Affilicious\Product\Model;

use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Aware_Product_Interface extends Product_Interface
{
    /**
     * Check if the product has a specific shop by the affiliate link.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link);

    /**
     * Add a new shop.
     *
     * @since 0.7
     * @param Shop_Interface $shop
     */
    public function add_shop(Shop_Interface $shop);

    /**
     * Remove the shop by the affiliate link.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the shop by the name.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop_Interface
     */
    public function get_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the cheapest shop.
     *
     * @since 0.7
     * @return null|Shop_Interface
     */
    public function get_cheapest_shop();

    /**
     * Get all shops.
     *
     * @since 0.7
     * @return Shop_Interface[]
     */
    public function get_shops();

    /**
     * Set all shops.
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.7
     * @param Shop_Interface[] $shops
     */
    public function set_shops($shops);
}
