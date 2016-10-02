<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ShopFactoryInterface extends FactoryInterface
{
    /**
     * Create a new shop
     *
     * @since 0.6
     * @param Title $title
     * @param AffiliateId $affiliateId
     * @param Currency $currency
     * @return Shop
     */
    public function create(Title $title, AffiliateId $affiliateId, Currency $currency);
}
