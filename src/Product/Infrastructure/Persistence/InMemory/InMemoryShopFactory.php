<?php
namespace Affilicious\Product\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Shop\AffiliateId;
use Affilicious\Product\Domain\Model\Shop\Currency;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopFactoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryShopFactory implements ShopFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, AffiliateId $affiliateId, Currency $currency)
    {
        $shop = new Shop(
            $title,
            $title->toName(),
            $affiliateId,
            $currency
        );

        return $shop;
    }
}
