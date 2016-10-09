<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\InvalidPriceCurrencyException;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop extends AbstractAggregate
{
    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Image
     */
    protected $thumbnail;

    /**
     * @var AffiliateId
     */
    protected $affiliateId;

    /**
     * @var AffiliateLink
     */
    protected $affiliateLink;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var Price
     */
    protected $oldPrice;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param AffiliateId $affiliateId
     * @param Currency $currency
     */
    public function __construct(
        Title $title,
        Name $name,
        AffiliateId $affiliateId,
        Currency $currency
    )
    {
        $this->title = $title;
        $this->name = $name;
        $this->affiliateId = $affiliateId;
        $this->currency = $currency;
    }

    /**
     * Get the title
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the name
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Check if the shop has a thumbnail
     *
     * @since 0.6
     * @return bool
     */
    public function hasThumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the thumbnail
     *
     * @since 0.6
     * @return Image
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the thumbnail
     *
     * @since 0.6
     * @param Image $thumbnail
     */
    public function setThumbnail(Image $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Get the affiliate ID
     *
     * @since 0.6
     * @return AffiliateId
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Check if the shop has an affiliate link
     *
     * @since 0.6
     * @return bool
     */
    public function hasAffiliateLink()
    {
        return $this->affiliateLink !== null;
    }

    /**
     * Get the affiliate link
     *
     * @since 0.6
     * @return AffiliateLink
     */
    public function getAffiliateLink()
    {
        return $this->affiliateLink;
    }

    /**
     * Set the affiliate link
     *
     * @since 0.6
     * @param AffiliateLink $affiliateLink
     */
    public function setAffiliateLink(AffiliateLink $affiliateLink)
    {
        $this->affiliateLink = $affiliateLink;
    }

    /**
     * Check if the shop has a price
     *
     * @since 0.6
     * @return bool
     */
    public function hasPrice()
    {
        return $this->price !== null;
    }

    /**
     * Get the price
     *
     * @since 0.6
     * @return null|Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the price or set it to null to keep it empty.
     *
     * @since 0.6
     * @param null|Price $price
     * @throws InvalidPriceCurrencyException
     */
    public function setPrice($price)
    {
        $this->checkPriceCurrency($price);
        $this->price = $price;
    }

    /**
     * Check if the shop has an old price
     *
     * @since 0.6
     * @return bool
     */
    public function hasOldPrice()
    {
        return $this->oldPrice !== null;
    }

    /**
     * Get the old price
     *
     * @since 0.6
     * @return null|Price
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * Set the old price or set it to null to keep it empty.
     *
     * @since 0.6
     * @param null|Price $oldPrice
     * @throws InvalidPriceCurrencyException
     */
    public function setOldPrice($oldPrice)
    {
        $this->checkPriceCurrency($oldPrice);
        $this->oldPrice = $oldPrice;
    }

    /**
     * Get the currency
     *
     * @since 0.6
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            ($this->hasThumbnail() && $this->getThumbnail()->isEqualTo($object->getThumbnail()) || !$object->hasThumbnail()) &&
            $this->getAffiliateId()->isEqualTo($object->getAffiliateId()) &&
            ($this->hasAffiliateLink() && $this->getAffiliateLink()->isEqualTo($object->getAffiliateLink()) || !$object->hasAffiliateLink()) &&
            ($this->hasPrice() && $this->getPrice()->isEqualTo($object->getPrice()) || !$object->hasPrice()) &&
            ($this->hasOldPrice() && $this->getOldPrice()->isEqualTo($object->getOldPrice()) || !$object->hasOldPrice()) &&
            $this->getCurrency()->isEqualTo($object->getCurrency());

    }

    /**
     * Check if the currency of the price matches the shop currency
     *
     * @since 0.6
     * @param Price $price
     * @throws InvalidPriceCurrencyException
     */
    protected function checkPriceCurrency($price)
    {
        if(!empty($price) && !$this->currency->isEqualTo($price->getCurrency())) {
            throw new InvalidPriceCurrencyException($price, $this->currency);
        }
    }
}
