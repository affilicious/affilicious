<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Product\Domain\Exception\DuplicatedDetailException;
use Affilicious\Product\Domain\Exception\DuplicatedShopException;
use Affilicious\Product\Domain\Model\Detail\Detail;
use Affilicious\Product\Domain\Model\Detail\Key;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Shop\ShopId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product extends AbstractEntity
{
    const POST_TYPE = 'product';
    const SLUG = 'product';

    const DETAIL_GROUP_ID = 'detail_group_id';
    const DETAIL_GROUP_DETAILS = 'details';
    const DETAIL_KEY = 'key';
    const DETAIL_TYPE = 'type';
    const DETAIL_LABEL = 'label';
    const DETAIL_VALUE = 'value';

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
     * @since 0.5.2
     * @param ProductId $id
     * @param Type $type
     * @param Title $title
     */
    public function __construct(ProductId $id, Type $type, Title $title)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->shops = array();
        $this->details = array();
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
        $this->imageGallery = array();
    }

    /**
     * Get the product ID
     *
     * @since 0.5.2
     * @return ProductId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the type like simple or complex
     *
     * @since 0.5.2
     * @return Type
     */
    public function getType()
    {
        return $this->type;
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
     * Check if the product has any content
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasContent()
    {
        return $this->content !== null;
    }

    /**
     * Get the content
     *
     * @since 0.5.2
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content
     *
     * @since 0.5.2
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Check if the product has a thumbnail
     *
     * @since 0.5.2
     * @return bool
     */
    public function hasThumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the thumbnail
     *
     * @since 0.5.2
     * @return Image
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the thumbnail
     *
     * @since 0.5.2
     * @param Image $thumbnail
     */
    public function setThumbnail(Image $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the product has a specific shop
     *
     * @since 0.5.2
     * @param ShopId $id
     * @return bool
     */
    public function hasShop(ShopId $id)
    {
        return isset($this->shops[$id->getValue()]);
    }

    /**
     * Add a new shop
     *
     * @since 0.5.2
     * @param Shop $shop
     * @throws DuplicatedShopException
     */
    public function addShop(Shop $shop)
    {
        if($this->hasShop($shop->getId())) {
            throw new DuplicatedShopException($shop, $this);
        }

        $this->shops[$shop->getId()->getValue()] = $shop;
    }

    /**
     * Remove a shop by the ID
     *
     * @since 0.5.2
     * @param ShopId $id
     */
    public function removeShop(ShopId $id)
    {
        unset($this->shops[$id->getValue()]);
    }

    /**
     * Get a shop by the ID
     *
     * @since 0.5.2
     * @param ShopId $id
     * @return null|Shop
     */
    public function getShop(ShopId $id)
    {
        $shop = $this->hasShop($id) ? $this->shops[$id->getValue()] : null;

        return $shop;
    }

    /**
     * Get the cheapest shop
     *
     * @since 0.5.2
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
     * @since 0.5.2
     * @return Shop[]
     */
    public function getShops()
    {
        $shops = array_values($this->shops);
        return $shops;
    }

    /**
     * Set all shops
     *
     * @since 0.5.2
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
     * Check if the product has a specific detail
     *
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
     * @param Key $key
     */
    public function removeDetail(Key $key)
    {
        unset($this->details[$key->getValue()]);
    }

    /**
     * Get a detail by the ID
     *
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
     * @return bool
     */
    public function hasReview()
    {
        return $this->review !== null;
    }

    /**
     * Get the review
     *
     * @since 0.5.2
     * @return Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set the review
     *
     * @since 0.5.2
     * @param Review $review
     */
    public function setReview(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the IDs of all related products
     *
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
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
     * @since 0.5.2
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        $post = get_post($this->id->getValue());
        return $post;
    }

    /**
     * @inheritdoc
     * @since 0.5.2
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self;
    }












    /**
     * Check if the product has a thumbnail
     *
     * @since 0.3
     * @return bool
     */
    public function hasThumbnail2()
    {
        $thumbnailId = get_post_thumbnail_id($this->getId());
        return $thumbnailId == false ? false : true;
    }

    /**
     * Get the thumbnail
     *
     * @since 0.3
     * @return null|string
     */
    public function getThumbnail2()
    {
        $thumbnailId = get_post_thumbnail_id($this->getId());
        if (!$thumbnailId) {
            return null;
        }

        $thumbnail = wp_get_attachment_image_src($thumbnailId, 'featured_preview');
        return $thumbnail[0];
    }
}
