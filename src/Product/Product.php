<?php
namespace Affilicious\ProductsPlugin\Product;

use Affilicious\ProductsPlugin\Product\Detail\DetailGroup;
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Product
{
    const POST_TYPE = 'product';
    const TAXONOMY = 'product_category';
    const SLUG = 'produktkategorie';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var PriceComparison
     */
    private $priceComparison;

    /**
     * @var FieldGroup[]
     */
    private $fieldGroups;

    /**
     * @var DetailGroup[]
     */
    private $detailGroups;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->detailGroups = array();
        $this->fieldGroups = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * Get the price comparison
     * @return PriceComparison
     */
    public function getPriceComparison()
    {
        return $this->priceComparison;
    }

    /**
     * Set the price comparison
     * @param PriceComparison $priceComparison
     */
    public function setPriceComparison(PriceComparison $priceComparison)
    {
        $this->priceComparison = $priceComparison;
    }

    /**
     * Add a field group to the product
     * @param FieldGroup $fieldGroup
     */
    public function addFieldGroup(FieldGroup $fieldGroup)
    {
        $this->fieldGroups[$fieldGroup->getId()] = $fieldGroup;
    }

    /**
     * Remove a field group from the product by the given ID
     * @param int $id
     */
    public function removeFieldGroup($id)
    {
        unset($this->fieldGroups[$id]);
    }

    /**
     * Check if the product has the field group with the given ID
     * @param int $id
     * @return bool
     */
    public function hasFieldGroup($id)
    {
        return isset($this->fieldGroups[$id]);
    }

    /**
     * Get the field group by the id
     * @param int $id
     * @return FieldGroup|null
     */
    public function getFieldGroup($id)
    {
        if (!$this->hasFieldGroup($id)) {
            return null;
        }

        return $this->fieldGroups[$id];
    }

    /**
     * Get all field groups from the product
     * @return FieldGroup[]
     */
    public function getFieldGroups()
    {
        return $this->fieldGroups;
    }

    /**
     * Count all field groups
     * @return int
     */
    public function countFieldGroups()
    {
        return count($this->fieldGroups);
    }

    /**
     * Add a detail group to the product
     * @param DetailGroup $detailGroup
     */
    public function addDetailGroup(DetailGroup $detailGroup)
    {
        $this->detailGroups[$detailGroup->getId()] = $detailGroup;
    }

    /**
     * Remove a detail group from the product by the given ID
     * @param int $id
     */
    public function removeDetailGroup($id)
    {
        unset($this->detailGroups[$id]);
    }

    /**
     * Check if the product has the detail group with the given ID
     * @param int $id
     * @return bool
     */
    public function hasDetailGroup($id)
    {
        return isset($this->detailGroups[$id]);
    }

    /**
     * Get the detail group by the id
     * @param int $id
     * @return DetailGroup|null
     */
    public function getDetailGroup($id)
    {
        if (!$this->hasDetailGroup($id)) {
            return null;
        }

        return $this->detailGroups[$id];
    }

    /**
     * Get all detail groups from the product
     * @return DetailGroup[]
     */
    public function getDetailGroups()
    {
        return $this->detailGroups;
    }

    /**
     * Count all detail groups
     * @return int
     */
    public function countDetailGroups()
    {
        return count($this->detailGroups);
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
