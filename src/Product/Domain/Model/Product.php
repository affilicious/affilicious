<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Product
{
    const POST_TYPE = 'product';
    const TAXONOMY = 'product_category';
    const SLUG = 'product';

    const DETAIL_GROUP_ID = 'detail_group_id';
    const DETAIL_GROUP_DETAILS = 'details';
    const DETAIL_KEY = 'key';
    const DETAIL_TYPE = 'type';
    const DETAIL_LABEL = 'label';
    const DETAIL_VALUE = 'value';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * European Article Number (EAN) is a unique ID used for identification of retail products
     * @var string
     */
    private $ean;

    /**
     * The specific shops with all information for the price comparison like Amazon, Affilinet or Ebay.
     * It's stored as an array where each entry is another key-value array for the specific shop
     * @var array
     */
    private $shops;

    /**
     * @var DetailGroup[]
     */
    private $detailGroups;

    /**
     * Stores the IDs of the related products
     * @var int[]
     */
    private $relatedProducts;

    /**
     * Stores the IDs of the related accessories
     * @var int[]
     */
    private $relatedAccessories;

    /**
     * Stores the IDs of the related posts
     * @var int[]
     */
    private $relatedPosts;

    /**
     * Stores the IDs of the image gallery attachments
     * @var int[]
     */
    private $imageGallery;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->shops = array();
        $this->detailGroups = array();
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
        $this->relatedPosts = array();
        $this->imageGallery = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->post->post_name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->post->post_content;
    }

    /**
     * Check if the product has a thumbnail
     * @return bool
     */
    public function hasThumbnail()
    {
        $thumbnailId = get_post_thumbnail_id($this->getId());
        return $thumbnailId == false ? false : true;
    }

    /**
     * Get the product thumbnail
     * @return null|string
     */
    public function getThumbnail()
    {
        $thumbnailId = get_post_thumbnail_id($this->getId());
        if (!$thumbnailId) {
            return null;
        }

        $thumbnail = wp_get_attachment_image_src($thumbnailId, 'featured_preview');
        return $thumbnail[0];
    }

    /**
     * Check if the price comparision has any European Article Number (EAN)
     * @return bool
     */
    public function hasEan()
    {
        return $this->ean !== null;
    }

    /**
     * Get the European Article Number (EAN)
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * Set the European Article Number (EAN)
     * @param string $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    /**
     * Get the shop by the ID
     *
     * @param int $shopId
     * @return null|array
     */
    public function getShop($shopId)
    {
        foreach ($this->shops as $shop) {
            if ($shop['shop_id'] === $shopId) {
                return $shop;
            }
        }

        return null;
    }

    /**
     * Get the cheapest shop
     *
     * @return null|array
     */
    public function getCheapestShop()
    {
        $cheapestShop = null;
        foreach ($this->shops as $shop) {
            if ($cheapestShop === null || $cheapestShop['price'] > $shop['price']) {
                $cheapestShop = $shop;
            }
        }

        return $cheapestShop;
    }

    /**
     * @return array
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * @param array $shops
     */
    public function setShops(array $shops)
    {
        $this->shops = $shops;
    }

    /**
     * Get all detail groups
     * @return DetailGroup[]
     */
    public function getDetailGroups()
    {
        return $this->detailGroups;
    }

    /**
     * Set the detail groups
     * @param array $detailGroups
     * @return DetailGroup|null
     */
    public function setDetailGroups(array $detailGroups)
    {
        $this->detailGroups = $detailGroups;
    }

    /**
     * @return int[]
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * @param int[] $relatedProducts
     */
    public function setRelatedProducts(array $relatedProducts)
    {
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * @return int[]
     */
    public function getRelatedAccessories()
    {
        return $this->relatedAccessories;
    }

    /**
     * @param int[] $relatedAccessories
     */
    public function setRelatedAccessories(array $relatedAccessories)
    {
        $this->relatedAccessories = $relatedAccessories;
    }

    /**
     * @return int[]
     */
    public function getRelatedPosts()
    {
        return $this->relatedPosts;
    }

    /**
     * @param int[] $relatedPosts
     */
    public function setRelatedPosts(array $relatedPosts)
    {
        $this->relatedPosts = $relatedPosts;
    }

    /**
     * @return int[]
     */
    public function getImageGallery()
    {
        return $this->imageGallery;
    }

    /**
     * @param int[] $imageGallery
     */
    public function setImageGallery(array $imageGallery)
    {
        $this->imageGallery = $imageGallery;
    }

    /**
     * Get the raw post
     * @return \WP_Post
     */
    public function getRawPost()
    {
        return $this->post;
    }
}
