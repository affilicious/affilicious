<?php
namespace Affilicious\Product\Model;

use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Shop;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Aware_Interface
{
    /**
     * Check if the product has a specific shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link);

    /**
     * Add a new product shop.
     *
     * @since 0.8
     * @param Shop $shop
     */
    public function add_shop(Shop $shop);

    /**
     * Remove the product shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the product shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop
     */
    public function get_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the cheapest product shop.
     *
     * @since 0.8
     * @return null|Shop
     */
    public function get_cheapest_shop();

    /**
     * Check if the product has any shops.
     *
     * @since 0.9
     * @return bool
     */
    public function has_shops();

    /**
     * Get all product shops.
     *
     * @since 0.8
     * @return Shop[]
     */
    public function get_shops();

    /**
     * Set all product shops.
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.8
     * @param Shop[] $shops
     */
    public function set_shops($shops);
}
