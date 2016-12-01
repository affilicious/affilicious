<?php
namespace Affilicious\Product\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Relation_Aware_Product_Interface extends Product_Interface
{
    /**
     * Get the IDs of all related products.
     *
     * @since 0.7
     * @return Product_Id[]
     */
    public function get_related_products();

    /**
     * Set the IDs of all related products.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.7
     * @param Product_Id[] $related_products
     */
    public function set_related_products($related_products);

    /**
     * Get the IDs of all related accessories.
     *
     * @since 0.7
     * @return Product_Id[]
     */
    public function get_related_accessories();

    /**
     * Set the IDs of all related accessories.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.7
     * @param Product_Id[] $related_accessories
     */
    public function set_related_accessories($related_accessories);
}
