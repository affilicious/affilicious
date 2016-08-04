<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Product
{
    const POST_TYPE = 'product';
    const TAXONOMY = 'product_category';
    const SLUG = 'product';

    const FIELD_GROUP_ID = 'field_group_id';
    const FIELD_GROUP_FIELDS = 'fields';
    const FIELD_KEY = 'key';
    const FIELD_TYPE = 'type';
    const FIELD_LABEL = 'label';
    const FIELD_VALUE = 'value';

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
     * @var FieldGroup[]
     */
    private $fieldGroups;

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
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->shops = array();
        $this->fieldGroups = array();
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
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
     * Get all field groups
     * @return FieldGroup[]
     */
    public function getFieldGroups()
    {
        return $this->fieldGroups;
    }

    /**
     * Set the field groups
     * @param array $fieldGroups
     * @return FieldGroup|null
     */
    public function setFieldGroups(array $fieldGroups)
    {
        $this->fieldGroups = $fieldGroups;
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
     * Get the raw post
     * @return \WP_Post
     */
    public function getRawPost()
    {
        return $this->post;
    }
}
