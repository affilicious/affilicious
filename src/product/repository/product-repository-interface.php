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
     * @param Product $product The product to store.
     * @return Product_Id|\WP_Error Either the stored product ID or an error on failure.
     */
    public function store(Product $product);

    /**
     * Delete the product by the ID.
     *
     * @since 0.9.16
     * @param Product_Id $product_id The product id of the product to delete.
     * @param bool $force_delete Optional. Whether to bypass trash and force deletion. Default false.
     * @return bool|\WP_Error It always returns true on success and an error on failure.
     */
    public function delete(Product_Id $product_id, $force_delete = false);

    /**
     * Delete all products by the args.
     *
     * @since 0.9.16
     * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all. Default empty.
     * @param bool $force_delete Optional. Whether to bypass trash and force deletion. Default false.
     * @return bool|\WP_Error It always returns true on success and an error on failure.
     */
    public function delete_all($args = [], $force_delete = false);

    /**
     * Find a product by the product ID.
     *
     * @since 0.9.16
     * @param Product_Id $product_id The product id of the product to find.
     * @return Product|null Either the product on success or an error on failure.
     */
    public function find(Product_Id $product_id);

    /**
     * Find all products by the args.
     *
     * @since 0.8
     * @param array $args Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all. Default empty.
     * @return Product[] An array of products.
     */
    public function find_all($args = []);

    /**
     * Find a product by the given ID.
     *
     * @deprecated 1.3 Use 'find' instead.
     * @since 0.8
     * @param Product_Id $product_id The product id of the product to find.
     * @return Product|null Either the product on success or an error on failure.
     */
    public function find_one_by_id(Product_Id $product_id);

    /**
     * Delete all variants from the parent complex product except the given ones.
     *
     * @deprecated 1.3 Use 'delete_all' instead.
     * @since 0.8.11
     * @param Product_Id $parent_product_id
     * @param Product_Variant[] $product_variants
     * @param bool $force_delete
     * @return Product_Id[]|\WP_Error Either the pro
     */
    public function delete_all_variants_except(Product_Id $parent_product_id, $product_variants, $force_delete = false);
}
