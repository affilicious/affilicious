<?php
namespace Affilicious\Product\Model;

use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Shop;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Shop_Aware_Trait
{
    /**
     * @var Shop[]
     */
    private $shops;

    /**
     * @since 0.8
     */
    public function __construct()
    {
        $this->shops = array();
    }

    /**
     * Check if the product has a specific shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link)
    {
        return isset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * Add a new product shop.
     *
     * @since 0.8
     * @param Shop $shop
     */
    public function add_shop(Shop $shop)
    {
        $this->shops[$shop->get_tracking()->get_affiliate_link()->get_value()] = $shop;
    }

    /**
     * Remove the product shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link)
    {
        unset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * Get the product shop by the affiliate link.
     *
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop
     */
    public function get_shop(Affiliate_Link $affiliate_link)
    {
        if(!$this->has_shop($affiliate_link)) {
            return null;
        }

        $shop = $this->shops[$affiliate_link->get_value()];

        return $shop;
    }

    /**
     * Get the cheapest product shop.
     *
     * @since 0.8
     * @return null|Shop
     */
    public function get_cheapest_shop()
    {
        $cheapest_shop = null;
        foreach ($this->shops as $shop) {
            if ($cheapest_shop === null || $shop->is_cheaper_than($cheapest_shop)) {
                $cheapest_shop = $shop;
            }
        }

        return $cheapest_shop;
    }

    /**
     * Get all product shops.
     *
     * @since 0.8
     * @return Shop[]
     */
    public function get_shops()
    {
        $shops = array_values($this->shops);

        return $shops;
    }

    /**
     * Set all product shops.
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.8
     * @param Shop[] $shops
     */
    public function set_shops($shops)
    {
        Assert::allIsInstanceOf($shops, Shop::class);

        $this->shops = $shops;
    }
}
