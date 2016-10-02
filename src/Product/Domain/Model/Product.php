<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\DuplicatedDetailException;
use Affilicious\Product\Domain\Exception\DuplicatedShopException;
use Affilicious\Product\Domain\Exception\DuplicatedVariantException;
use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Shop\AffiliateId;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product extends AbstractEntity
{
    const POST_TYPE = 'product';
    const SLUG = 'product';

    /**
     * The unique ID of the product
     *
     * @var ProductId
     */
    protected $id;

    /**
     * The type of the product like simple or variants
     *
     * @var Type
     */
    protected $type;

    /**
     * The title of the product
     *
     * @var Title
     */
    protected $title;

    /**
     * The name of the product
     *
     * @var Name
     */
    protected $name;

    /**
     * The content of the product
     *
     * @var Content
     */
    protected $content;

    /**
     * The thumbnail of the product
     *
     * @var Image
     */
    protected $thumbnail;

    /**
     * Holds the shops like Amazon, Affilinet or Ebay.
     *
     * @var Shop[]
     */
    protected $shops;

    /**
     * Holds all product variants
     *
     * @var ProductVariant[]
     */
    protected $variants;

    /**
     * Holds the details of the product
     *
     * @var Detail[]
     */
    protected $details;

    /**
     * Stores the rating in 0.5 steps from 0 to 5 and the number of votes
     *
     * @var Review
     */
    protected $review;

    /**
     * Stores the IDs of the related products
     *
     * @var int[]
     */
    protected $relatedProducts;

    /**
     * Stores the IDs of the related accessories
     *
     * @var int[]
     */
    protected $relatedAccessories;

    /**
     * Stores the IDs of the image gallery attachments
     *
     * @var int[]
     */
    protected $imageGallery;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     */
    public function __construct(Title $title, Name $name)
    {
        $this->title = $title;
        $this->name = $name;
        $this->type = Type::simple();
        $this->shops = array();
        $this->variants = array();
        $this->details = array();
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
        $this->imageGallery = array();
    }

    /**
     * Check if the product has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the product ID
     *
     * @since 0.6
     * @return ProductId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the product ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|ProductId $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the type like simple or variants.
     *
     * @since 0.6
     * @return Type
     */
    public function getType()
    {
        $type = count($this->variants) == 0 ? Type::simple() : Type::variants();

        return $type;
    }

    /**
     * Set the type like simple or variants
     *
     * @since 0.6
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
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
     * Set the title
     *
     * @since 0.6
     * @param Title $title
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
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
     * Set the name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

    /**
     * Check if the product has any content
     *
     * @since 0.6
     * @return bool
     */
    public function hasContent()
    {
        return $this->content !== null;
    }

    /**
     * Get the content
     *
     * @since 0.6
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content
     *
     * @since 0.6
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Check if the product has a thumbnail
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
     * Check if the product has a specific shop by the affiliate ID
     *
     * @since 0.6
     * @param AffiliateId $affiliateId
     * @return bool
     */
    public function hasShop(AffiliateId $affiliateId)
    {
        return isset($this->shops[$affiliateId->getValue()]);
    }

    /**
     * Add a new shop
     *
     * @since 0.6
     * @param Shop $shop
     * @throws DuplicatedShopException
     */
    public function addShop(Shop $shop)
    {
        if($this->hasShop($shop->getAffiliateId())) {
            throw new DuplicatedShopException($shop, $this);
        }

        $this->shops[$shop->getAffiliateId()->getValue()] = $shop;
    }

    /**
     * Remove a shop by the affiliate ID
     *
     * @since 0.6
     * @param AffiliateId $affiliateId
     */
    public function removeShop(AffiliateId $affiliateId)
    {
        unset($this->shops[$affiliateId->getValue()]);
    }

    /**
     * Get a shop by the affiliate ID
     *
     * @since 0.6
     * @param AffiliateId $affiliateId
     * @return null|Shop
     */
    public function getShop(AffiliateId $affiliateId)
    {
        $shop = $this->hasShop($affiliateId) ? $this->shops[$affiliateId->getValue()] : null;

        return $shop;
    }

    /**
     * Get the cheapest shop
     *
     * @since 0.6
     * @return null|Shop
     */
    public function getCheapestShop()
    {
        /** @var Shop $cheapestShop */
        $cheapestShop = null;
        foreach ($this->shops as $shop) {
            if ($cheapestShop === null ||
                ($cheapestShop->hasPrice() && $cheapestShop->getPrice()->isGreaterThan($shop->hasPrice()))) {
                $cheapestShop = $shop;
            }
        }

        return $cheapestShop;
    }

    /**
     * Get all shops
     *
     * @since 0.6
     * @return Shop[]
     */
    public function getShops()
    {
        if(empty($this->variants)) {
            return array();
        }

        $shops = array_values($this->shops);
        return $shops;
    }

    /**
     * Set all shops
     *
     * @since 0.6
     * @param Shop[] $shops
     * @throws InvalidTypeException
     */
    public function setShops($shops)
    {
        $this->shops = array();

        // addShops checks for the type
        foreach ($shops as $shop) {
            $this->addShop($shop);
        }
    }

    /**
     * Check if the product has a specific variant
     *
     * @since 0.6
     * @param ProductId $id
     * @return bool
     */
    public function hasVariant(ProductId $id)
    {
        return isset($this->variants[$id->getValue()]);
    }

    /**
     * Add a new product variant
     *
     * @since 0.6
     * @param ProductVariant $variant
     */
    public function addVariant(ProductVariant $variant)
    {
        if(!$variant->hasId()) {
            throw new \RuntimeException(sprintf(
                'The product variant %s has no ID.',
                $variant->getTitle()
            ));
        }

        if($this->hasVariant($variant->getId())) {
            throw new DuplicatedVariantException($variant, $this);
        }

        $this->variants[$variant->getId()->getValue()] = $variant;
    }

    /**
     * Remove an existing product variant
     *
     * @since 0.6
     * @param ProductVariant $variant
     */
    public function removeVariant(ProductVariant $variant)
    {
        if(!$variant->hasId()) {
            throw new \RuntimeException(sprintf(
                'The product variant %s has no ID.',
                $variant->getTitle()
            ));
        }

        unset($this->variants[$variant->getId()->getValue()]);
    }

    /**
     * Get the product variant by the ID
     *
     * @since 0.6
     * @param ProductId $id
     * @return null|ProductVariant
     */
    public function getVariant(ProductId $id)
    {
        $variant = $this->hasVariant($id) ? $this->variants[$id->getValue()] : null;

        return $variant;
    }

    /**
     * Get all product variants
     *
     * @since 0.6
     * @return ProductVariant[]
     */
    public function getVariants()
    {
        $variants = array_values($this->variants);

        return $variants;
    }

    /**
     * Set all product variants
     *
     * @since 0.6
     * @param ProductVariant[] $variants
     * @throws InvalidTypeException
     */
    public function setVariants($variants)
    {
        $this->variants = array();

        // addShops checks for the type
        foreach ($variants as $variant) {
            $this->addVariant($variant);
        }
    }

    /**
     * Check if the product has a specific detail
     *
     * @since 0.6
     * @param Key $key
     * @return bool
     */
    public function hasDetail(Key $key)
    {
        return isset($this->details[$key->getValue()]);
    }

    /**
     * Add a new detail
     *
     * @since 0.6
     * @param Detail $detail
     * @throws DuplicatedDetailException
     */
    public function addDetail(Detail $detail)
    {
        if($this->hasDetail($detail->getKey())) {
            throw new DuplicatedDetailException($detail, $this);
        }

        $this->details[$detail->getKey()->getValue()] = $detail;
    }

    /**
     * Remove a detail by the ID
     *
     * @since 0.6
     * @param Key $key
     */
    public function removeDetail(Key $key)
    {
        unset($this->details[$key->getValue()]);
    }

    /**
     * Get a detail by the ID
     *
     * @since 0.6
     * @param Key $key
     * @return null|Detail
     */
    public function getDetail(Key $key)
    {
        $detail = $this->hasDetail($key) ? $this->details[$key->getValue()] : null;

        return $detail;
    }

    /**
     * Get all details
     *
     * @since 0.6
     * @return Detail[]
     */
    public function getDetails()
    {
        $details = array_values($this->details);
        return $details;
    }

    /**
     * Set all details
     *
     * @since 0.6
     * @param Detail[] $details
     * @throws InvalidTypeException
     */
    public function setDetails($details)
    {
        $this->details = array();

        // addDetail checks for the type
        foreach ($details as $detail) {
            $this->addDetail($detail);
        }
    }

    /**
     * Check if the product has a review
     *
     * @since 0.6
     * @return bool
     */
    public function hasReview()
    {
        return $this->review !== null;
    }

    /**
     * Get the review
     *
     * @since 0.6
     * @return Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set the review
     *
     * @since 0.6
     * @param Review $review
     */
    public function setReview(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the IDs of all related products
     *
     * @since 0.6
     * @return ProductId[]
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Set the IDs of all related products
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param ProductId[] $relatedProducts
     * @throws InvalidTypeException
     */
    public function setRelatedProducts($relatedProducts)
    {
        foreach ($relatedProducts as $relatedProduct) {
            if (!($relatedProduct instanceof ProductId)) {
                throw new InvalidTypeException($relatedProduct, get_class(new ProductId(0)));
            }
        }

        $this->relatedProducts = $relatedProducts;
    }

    /**
     * Get the IDs of all related accessories
     *
     * @since 0.6
     * @return ProductId[]
     */
    public function getRelatedAccessories()
    {
        return $this->relatedAccessories;
    }

    /**
     * Set the IDs of all related accessories
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param ProductId[] $relatedAccessories
     * @throws InvalidTypeException
     */
    public function setRelatedAccessories($relatedAccessories)
    {
        foreach ($relatedAccessories as $relatedAccessory) {
            if (!($relatedAccessory instanceof ProductId)) {
                throw new InvalidTypeException($relatedAccessory, 'Affilicious\Product\Domain\Model\ProductId');
            }
        }

        $this->relatedAccessories = $relatedAccessories;
    }

    /**
     * Get the IDs of the media attachments for the image gallery
     *
     * @since 0.6
     * @return Image[]
     */
    public function getImageGallery()
    {
        return $this->imageGallery;
    }

    /**
     * Set the IDs of the media attachments for the image gallery
     * If you do this, the old images going to be replaced.
     *
     * @since 0.6
     * @param Image[] $imageGallery
     * @throws InvalidTypeException
     */
    public function setImageGallery($imageGallery)
    {
        foreach ($imageGallery as $image) {
            if (!($image instanceof Image)) {
                throw new InvalidTypeException($image, 'Affilicious\Common\Domain\Model\Image\Image');
            }
        }

        $this->imageGallery = $imageGallery;
    }

    /**
     * Get the raw Wordpress post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getId()->isEqualTo($object->getId());
    }
}
