<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Relation_Aware_Trait
{
    /**
     * The IDs of all related products.
     *
     * @var Product_Id[]
     */
    private $related_products;

    /**
     * The IDs of all related accessories.
     *
     * @var Product_Id[]
     */
    private $related_accessories;

    /**
     * @since 0.8
     */
    public function __construct()
    {
        $this->related_products = array();
        $this->related_accessories = array();
    }

    /**
     * Check if the product has any related products.
     *
     * @since 0.9
     * @return bool
     */
    public function has_related_products()
    {
        return !empty($this->related_products);
    }

    /**
     * Get the IDs of all related products.
     *
     * @since 0.8
     * @return Product_Id[]
     */
    public function get_related_products()
    {
        $related_products = array_values($this->related_products);

        return $related_products;
    }

    /**
     * Set the IDs of all related products.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.8
     * @param Product_Id[] $related_products
     */
    public function set_related_products($related_products)
    {
    	Assert_Helper::all_is_instance_of($related_products, Product_Id::class, __METHOD__, 'Expected an array of product IDs. But one of the values is %s', '0.9.2');

        $this->related_products = $related_products;
    }

    /**
     * Check if the product has any related accessories.
     *
     * @since 0.9
     * @return bool
     */
    public function has_related_accessories()
    {
        return !empty($this->related_products);
    }

    /**
     * Get the IDs of all related accessories.
     *
     * @since 0.8
     * @return Product_Id[]
     */
    public function get_related_accessories()
    {
        $related_accessories = array_values($this->related_accessories);

        return $related_accessories;
    }

    /**
     * Set the IDs of all related accessories.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.8
     * @param Product_Id[] $related_accessories
     */
    public function set_related_accessories($related_accessories)
    {
	    Assert_Helper::all_is_instance_of($related_accessories, Product_Id::class, __METHOD__, 'Expected an array of product IDs. But one of the values is %s', '0.9.2');

        $this->related_accessories = $related_accessories;
    }
}
