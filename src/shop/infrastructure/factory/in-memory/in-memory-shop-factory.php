<?php
namespace Affilicious\Shop\Infrastructure\Factory\In_Memory;

use Affilicious\Product\Infrastructure\Repository\Carbon\Carbon_Product_Repository;
use Affilicious\Shop\Domain\Model\Affiliate_Id;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Currency;
use Affilicious\Shop\Domain\Model\Price;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\Shop_Factory_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template_Id;
use Affilicious\Shop\Domain\Model\Shop_Template_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Shop_Factory implements Shop_Factory_Interface
{
    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @since 0.6
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(Shop_Template_Repository_Interface $shop_template_repository)
    {
        $this->shop_template_repository = $shop_template_repository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Shop_Template_Interface $shop_template, Affiliate_Link $affiliate_link, Currency $currency)
    {
        $shop = new Shop($shop_template, $affiliate_link, $currency);

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create_from_template_id_and_data(Shop_Template_Id $shop_template_id, $data)
    {
        if(empty($data)) {
            return null;
        }

        $shop_template = $this->shop_template_repository->find_by_id($shop_template_id);
        if($shop_template === null || !is_array($data)) {
            return null;
        }

        $affiliate_link = !empty($data[Carbon_Product_Repository::SHOP_AFFILIATE_LINK]) ? $data[Carbon_Product_Repository::SHOP_AFFILIATE_LINK] : null;
        $affiliate_id = !empty($data[Carbon_Product_Repository::SHOP_AFFILIATE_ID]) ? $data[Carbon_Product_Repository::SHOP_AFFILIATE_ID] : null;
        $price = !empty($data[Carbon_Product_Repository::SHOP_PRICE]) ? number_format(floatval($data[Carbon_Product_Repository::SHOP_PRICE]),  2, '.', '') : null;
        $old_price = !empty($data[Carbon_Product_Repository::SHOP_OLD_PRICE]) ? number_format(floatval($data[Carbon_Product_Repository::SHOP_OLD_PRICE]),  2, '.', '') : null;
        $delivery_rates = !empty($data[Carbon_Product_Repository::SHOP_DELIVERY_RATES]) ? number_format(floatval($data[Carbon_Product_Repository::SHOP_DELIVERY_RATES]),  2, '.', '') : null;
        $currency = !empty($data[Carbon_Product_Repository::SHOP_CURRENCY]) ? $data[Carbon_Product_Repository::SHOP_CURRENCY] : null;
        $updated_at = !empty($data[Carbon_Product_Repository::SHOP_UPDATED_AT]) ? $data[Carbon_Product_Repository::SHOP_UPDATED_AT] : null;

        if(empty($affiliate_link) || empty($currency)) {
            return null;
        }

        // Legacy support
        if(empty($updated_at)) {
            $updated_at = date('Y-m-d H:i:s');
        }

        $shop = $this->create(
            $shop_template,
            new Affiliate_Link($affiliate_link),
            new Currency($currency)
        );

        if($shop_template->has_thumbnail()) {
            $shop->set_thumbnail($shop_template->get_thumbnail());
        }

        if(!empty($affiliate_id)) {
            $shop->set_affiliate_id(new Affiliate_Id($affiliate_id));
        }

        if(!empty($price)) {
            $shop->set_price(new Price($price, $shop->get_currency()));
        }

        if(!empty($old_price)) {
            $shop->set_old_price(new Price($old_price, $shop->get_currency()));
        }

        if(!empty($delivery_rates)) {
            $shop->set_delivery_rates(new Price($delivery_rates, $shop->get_currency()));
        }

        if(!empty($updated_at)) {
            $shop->set_updated_at(new \DateTimeImmutable($updated_at));
        }

        return $shop;
    }
}
