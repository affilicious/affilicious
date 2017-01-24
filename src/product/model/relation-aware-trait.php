<?php
namespace Affilicious\Product\Model;

use Webmozart\Assert\Assert;

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
        Assert::allIsInstanceOf($related_products, Product_Id::class);

        $this->related_products = $related_products;
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
        Assert::allIsInstanceOf($related_accessories, Product_Id::class);

        $this->related_accessories = $related_accessories;
    }
}
