<?php
namespace Affilicious\Product\Domain\Model;

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
     *
     * @var string
     */
    private $ean;

    /**
     * The specific shops with all information for the price comparison like Amazon, Affilinet or Ebay.
     * It's stored as an array where each entry is another key-value array for the specific shop
     *
     * @var array
     */
    private $shops;

    /**
     * @var DetailGroup[]
     */
    private $detailGroups;

	/**
	 * Get the number of ratings
	 *
	 * @var int
	 */
	PRIVATE $numberOfRatings;

	/**
	 * Stores the number of stars for the rating in 0.5 steps from 0 to 5
	 *
	 * @var float
	 */
	private $starRating;

    /**
     * Stores the IDs of the related products
     *
     * @var int[]
     */
    private $relatedProducts;

    /**
     * Stores the IDs of the related accessories
     *
     * @var int[]
     */
    private $relatedAccessories;

    /**
     * Stores the IDs of the related posts
     *
     * @var int[]
     */
    private $relatedPosts;

    /**
     * Stores the IDs of the image gallery attachments
     *
     * @var int[]
     */
    private $imageGallery;

    /**
     * @since 0.3
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->shops = array();
        $this->detailGroups = array();
	    $this->numberOfRatings = 0;
	    $this->starRating = 0;
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
        $this->relatedPosts = array();
        $this->imageGallery = array();
    }

    /**
     * Get the ID
     *
     * @since 0.3
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * Get the title
     *
     * @since 0.3
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }

    /**
     * Get the name
     *
     * @since 0.3
     * @return string
     */
    public function getName()
    {
        return $this->post->post_name;
    }

    /**
     * Get the content
     *
     * @since 0.3
     * @return string
     */
    public function getContent()
    {
        return $this->post->post_content;
    }

    /**
     * Check if the product has a thumbnail
     *
     * @since 0.3
     * @return bool
     */
    public function hasThumbnail()
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
     *
     * @since 0.3
     * @return bool
     */
    public function hasEan()
    {
        return $this->ean !== null;
    }

    /**
     * Get the European Article Number (EAN)
     *
     * @since 0.3
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * Set the European Article Number (EAN)
     *
     * @since 0.3
     * @param string $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    /**
     * Get the shop by the ID
     *
     * @since 0.3
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
     * @since 0.3
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
     * Get all shops
     *
     * @since 0.3
     * @return array
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Set all shops.
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.3
     * @param array $shops
     */
    public function setShops($shops)
    {
        $this->shops = $shops;
    }

	/**
	 * Get the number of ratings
	 *
	 * @since 0.3.4
	 * @return int
	 */
	public function getNumberOfRatings()
	{
		return $this->numberOfRatings;
	}

	/**
	 * Set the number of ratings
	 *
	 * @since 0.3.4
	 * @param int $numberOfRatings
	 */
	public function setNumberOfRatings($numberOfRatings)
	{
		$this->numberOfRatings = $numberOfRatings;
	}

	/**
	 * Get the star rating from 0 to 5 in 0.5 steps
	 *
	 * @since 0.3.3
	 * @return float
	 */
	public function getStarRating()
	{
		return $this->starRating;
	}

	/**
	 * Get the star rating from 0 to 5 in 0.5 steps
	 *
	 * @since 0.3.3
	 * @param float $starRating
	 */
	public function setStarRating($starRating)
	{
		$this->starRating = $starRating;
	}

    /**
     * Get all detail groups
     *
     * @since 0.3
     * @return DetailGroup[]
     */
    public function getDetailGroups()
    {
        return $this->detailGroups;
    }

    /**
     * Set the detail groups
     * If you do this, the old detail groups going to be replaced.
     *
     * @since 0.3
     * @param array $detailGroups
     * @return DetailGroup|null
     */
    public function setDetailGroups($detailGroups)
    {
        $this->detailGroups = $detailGroups;
    }

    /**
     * Get the IDs of all related products
     *
     * @since 0.3
     * @return int[]
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Set the IDs of all related products
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.3
     * @param int[] $relatedProducts
     */
    public function setRelatedProducts($relatedProducts)
    {
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * Get the IDs of all related accessories
     *
     * @since 0.3
     * @return int[]
     */
    public function getRelatedAccessories()
    {
        return $this->relatedAccessories;
    }

    /**
     * Set the IDs of all related accessories
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.3
     * @param int[] $relatedAccessories
     */
    public function setRelatedAccessories($relatedAccessories)
    {
        $this->relatedAccessories = $relatedAccessories;
    }

    /**
     * Get the IDs of all related posts
     *
     * @since 0.3
     * @return int[]
     */
    public function getRelatedPosts()
    {
        return $this->relatedPosts;
    }

    /**
     * Set the IDs of all related posts
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.3
     * @param int[] $relatedPosts
     */
    public function setRelatedPosts($relatedPosts)
    {
        $this->relatedPosts = $relatedPosts;
    }

    /**
     * Get the IDs of the media attachments for the image gallery
     *
     * @since 0.3
     * @return int[]
     */
    public function getImageGallery()
    {
        return $this->imageGallery;
    }

    /**
     * Set the IDs of the media attachments for the image gallery
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.3
     * @param int[] $imageGallery
     */
    public function setImageGallery($imageGallery)
    {
        $this->imageGallery = $imageGallery;
    }

    /**
     * Get the raw post
     *
     * @since 0.3
     * @return \WP_Post
     */
    public function getRawPost()
    {
        return $this->post;
    }
}
