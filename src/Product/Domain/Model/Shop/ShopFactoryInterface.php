<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\ShopTemplateId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ShopFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new shop which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param AffiliateLink $affiliateLink
     * @param Currency $currency
     * @return Shop
     */
    public function create(Title $title, Name $name, Key $key, AffiliateLink $affiliateLink, Currency $currency);

    /**
     * Create a new shop from the template.
     *
     * @since 0.6
     * @param ShopTemplateId $shopTemplateId
     * @param mixed $data The structure of the data varies and depends on the implementation
     * @return null|Shop
     */
    public function createFromTemplateIdAndData(ShopTemplateId $shopTemplateId, $data);
}
