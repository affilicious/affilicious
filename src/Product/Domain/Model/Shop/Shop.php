<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop extends AbstractAggregate
{
    /**
     * This ID has the same value as the IDs of the shops from the shop module,
     * but is a different value object.
     *
     * @var ShopId
     */
    private $id;

    /**
     * @var Title
     */
    private $title;

    /**
     * @var Logo
     */
    private $logo;

    /**
     * @var Price
     */
    private $price;

    /**
     * @var Price
     */
    private $oldPrice;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var AffiliateId
     */
    private $affiliateId;

    /**
     * @var AffiliateLink
     */
    private $affiliateLink;

    /**
     * @since 0.5.2
     * @param ShopId $id
     * @param Title $title
     * @param Currency $currency
     */
    public function __construct(ShopId $id, Title $title, Currency $currency)
    {
        $this->id = $id;
        $this->title = $title;
        $this->currency = $currency;
    }

    /**
     * Get the shop ID
     *
     * @since 0.5.2
     * @return ShopId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the title
     *
     * @since 0.5.2
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Check if the shop has a logo
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasLogo()
    {
        return $this->logo !== null;
    }

    /**
     * Get the logo
     *
     * @since 0.5.2
     * @return Logo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the logo
     *
     * @since 0.5.2
     * @param Logo $logo
     */
    public function setLogo(Logo $logo)
    {
        $this->logo = $logo;
    }

    /**
     * Check if the shop has a price
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasPrice()
    {
        return $this->price !== null;
    }

    /**
     * Get the price
     *
     * @since 0.5.2
     * @return null|Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the price or set it to null to keep it empty.
     *
     * @since 0.5.2
     * @param null|Price $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Check if the shop has an old price
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasOldPrice()
    {
        return $this->oldPrice !== null;
    }

    /**
     * Get the old price
     *
     * @since 0.5.2
     * @return null|Price
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * Set the old price or set it to null to keep it empty.
     *
     * @since 0.5.2
     * @param null|Price $oldPrice
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
    }

    /**
     * Get the currency
     *
     * @since 0.5.2
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Check if the shop has an affiliate ID
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasAffiliateId()
    {
        return $this->affiliateId !== null;
    }

    /**
     * Get the affiliate ID
     *
     * @since 0.5.2
     * @return AffiliateId
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Set the affiliate ID
     *
     * @since 0.5.2
     * @param AffiliateId $affiliateId
     */
    public function setAffiliateId(AffiliateId $affiliateId)
    {
        $this->affiliateId = $affiliateId;
    }

    /**
     * Check if the shop has an affiliate link
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasAffiliateLink()
    {
        return $this->affiliateLink !== null;
    }

    /**
     * Get the affiliate link
     *
     * @since 0.5.2
     * @return AffiliateLink
     */
    public function getAffiliateLink()
    {
        return $this->affiliateLink;
    }

    /**
     * Set the affiliate link
     *
     * @since 0.5.2
     * @param AffiliateLink $affiliateLink
     */
    public function setAffiliateLink(AffiliateLink $affiliateLink)
    {
        $this->affiliateLink = $affiliateLink;
    }

    /**
     * @inheritdoc
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getId()->isEqualTo($object->getId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getCurrency()->isEqualTo($object->getCurrency()) &&
            ($this->hasLogo() && $this->getLogo()->isEqualTo($object->getLogo()) || !$object->hasLogo()) &&
            ($this->hasAffiliateId() && $this->getAffiliateId()->isEqualTo($object->getAffiliateId()) || !$object->hasAffiliateId()) &&
            ($this->hasAffiliateLink() && $this->getAffiliateLink()->isEqualTo($object->getAffiliateLink()) || !$object->hasAffiliateLink()) &&
            ($this->hasPrice() && $this->getPrice()->isEqualTo($object->getPrice()) || !$object->hasPrice()) &&
            ($this->hasOldPrice() && $this->getOldPrice()->isEqualTo($object->getOldPrice()) || !$object->hasOldPrice());
    }
}
