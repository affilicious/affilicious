<?php
namespace Affilicious\Product\Repository;

use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product_Variant;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Repository_Interface
{
    /**
     * Store the product.
     *
     * @since 0.8
     * @param Product $product
     * @return Product_Id|\WP_Error
     */
    public function store(Product $product);

    /**
     * Delete the product by the ID.
     *
     * @since 0.8
     * @param Product_Id $product_id
     * @param bool $force_delete
     * @return Product|\WP_Error
     */
    public function delete(Product_Id $product_id, $force_delete = false);

    /**
     * Find a product by the given ID.
     *
     * @since 0.8
     * @param Product_Id $product_id
     * @return Product|null
     */
    public function find_one_by_id(Product_Id $product_id);

    /**
     * Find all products.
     *
     * @since 0.8
     * @param array $args
     * @return Product[]
     */
    public function find_all($args = array());

    /**
     * Delete all variants from the parent complex product except the given ones.
     *
     * @since 0.8.11
     * @param Product_Id $parent_product_id
     * @param Product_Variant[] $product_variants
     * @param bool $force_delete
     * @return Product_Id[]|\WP_Error
     */
    public function delete_all_variants_except(Product_Id $parent_product_id, $product_variants, $force_delete = false);
}
