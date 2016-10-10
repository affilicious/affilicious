<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\InvalidPriceCurrencyException;
use Affilicious\Shop\Domain\Model\ShopTemplateId;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop extends AbstractAggregate
{
    /**
     * @var ShopTemplateId
     */
    protected $templateId;

    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

    /**
     * @var Image
     */
    protected $thumbnail;

    /**
     * @var AffiliateLink
     */
    protected $affiliateLink;

    /**
     * @var AffiliateId
     */
    protected $affiliateId;

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
     * @param Key $key
     * @param AffiliateLink $affiliateLink
     * @param Currency $currency
     */
    public function __construct(Title $title, Name $name, Key $key, AffiliateLink $affiliateLink, Currency $currency)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->affiliateLink = $affiliateLink;
        $this->currency = $currency;
    }

    /**
     * Check if the shop has a template ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasTemplateId()
    {
        return $this->templateId !== null;
    }

    /**
     * Get the shop template ID
     *
     * @since 0.6
     * @return null|ShopTemplateId
     *
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set the shop template ID
     *
     * @since 0.6
     * @param null|ShopTemplateId
     * $templateId
     * @throws InvalidTypeException
     */
    public function setTemplateId($templateId)
    {
        if($templateId !== null && !($templateId instanceof ShopTemplateId)) {
            throw new InvalidTypeException($templateId, 'Affilicious\Shop\Domain\Model\ShopTemplateId');
        }

        $this->templateId = $templateId;
    }

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
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
     * Get the optional thumbnail
     *
     * @since 0.6
     * @return null|Image
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the optional thumbnail
     *
     * @since 0.6
     * @param null|Image $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof AffiliateId)) {
            throw new InvalidTypeException($thumbnail, 'Affilicious\Common\Domain\Model\Image\Image');
        }

        $this->thumbnail = $thumbnail;
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
     * Check if the shop has an affiliate ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasAffiliateId()
    {
        return $this->affiliateId !== null;
    }

    /**
     * Get the optional affiliate ID
     *
     * @since 0.6
     * @return AffiliateId
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Set the optional affiliate ID
     *
     * @since 0.6
     * @param null|AffiliateId $affiliateId
     */
    public function setAffiliateId($affiliateId)
    {
        if($affiliateId !== null && !($affiliateId instanceof AffiliateId)) {
            throw new InvalidTypeException($affiliateId, 'Affilicious\Product\Domain\Model\Shop\AffiliateId');
        }

        $this->affiliateId = $affiliateId;
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
            ($this->hasTemplateId() && $this->getTemplateId()->isEqualTo($object->getTemplateId()) || !$object->hasTemplateId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            $this->getKey()->isEqualTo($object->getKey()) &&
            ($this->hasThumbnail() && $this->getThumbnail()->isEqualTo($object->getThumbnail()) || !$object->hasThumbnail()) &&
            ($this->hasAffiliateId() && $this->getAffiliateId()->isEqualTo($object->getAffiliateId()) || !$object->hasAffiliateId()) &&
            $this->getAffiliateLink()->isEqualTo($object->getAffiliateLink()) &&
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
