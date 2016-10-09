<?php
namespace Affilicious\Product\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Shop\AffiliateId;
use Affilicious\Product\Domain\Model\Shop\AffiliateLink;
use Affilicious\Product\Domain\Model\Shop\Currency;
use Affilicious\Product\Domain\Model\Shop\Price;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopFactoryInterface;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Shop\Domain\Model\ShopTemplateId;
use Affilicious\Shop\Domain\Model\ShopTemplateRepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryShopFactory implements ShopFactoryInterface
{
    /**
     * @var ShopTemplateRepositoryInterface
     */
    private $shopTemplateRepository;

    /**
     * @since 0.6
     * @param ShopTemplateRepositoryInterface $shopTemplateRepository
     */
    public function __construct(ShopTemplateRepositoryInterface $shopTemplateRepository)
    {
        $this->shopTemplateRepository = $shopTemplateRepository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name, Key $key, AffiliateLink $affiliateLink, Currency $currency)
    {
        $shop = new Shop($title, $name, $key, $affiliateLink, $currency);

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function createFromTemplateIdAndData(ShopTemplateId $shopTemplateId, $data)
    {
        $shopTemplate = $this->shopTemplateRepository->findById($shopTemplateId);
        if($shopTemplate === null || !is_array($data)) {
            return null;
        }

        $affiliateLink = !empty($data[CarbonProductRepository::SHOP_AFFILIATE_LINK]) ? $data[CarbonProductRepository::SHOP_AFFILIATE_LINK] : null;
        $affiliateId = !empty($data[CarbonProductRepository::SHOP_AFFILIATE_ID]) ? $data[CarbonProductRepository::SHOP_AFFILIATE_ID] : null;
        $price = !empty($data[CarbonProductRepository::SHOP_PRICE]) ? floatval($data[CarbonProductRepository::SHOP_PRICE]) : null;
        $oldPrice = !empty($data[CarbonProductRepository::SHOP_OLD_PRICE]) ? floatval($data[CarbonProductRepository::SHOP_OLD_PRICE]) : null;
        $currency = !empty($data[CarbonProductRepository::SHOP_CURRENCY]) ? $data[CarbonProductRepository::SHOP_CURRENCY] : null;

        if(empty($affiliateLink) || empty($currency)) {
            return null;
        }

        $shop = $this->create(
            $shopTemplate->getTitle(),
            $shopTemplate->getName(),
            $shopTemplate->getKey(),
            new AffiliateLink($affiliateLink),
            new Currency($currency)
        );

        $shop->setTemplateId($shopTemplateId);

        if($shopTemplate->hasThumbnail()) {
            $shop->setThumbnail($shopTemplate->getThumbnail());
        }

        if(!empty($affiliateId)) {
            $shop->setAffiliateId(new AffiliateId($affiliateId));
        }

        if(!empty($price)) {
            $shop->setPrice(new Price($price, $shop->getCurrency()));
        }

        if(!empty($oldPrice)) {
            $shop->setOldPrice(new Price($oldPrice, $shop->getCurrency()));
        }

        return $shop;
    }
}
