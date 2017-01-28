<?php
namespace Affilicious\Product\Repository;

use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;

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
     * @return Product
     */
    public function store(Product $product);

    /**
     * Store all products.
     *
     * @since 0.8
     * @param $products
     * @return Product[]
     */
    public function store_all($products);

    /**
     * Delete the product.
     *
     * @since 0.8
     * @param Product_Id $product_id
     * @return Product
     */
    public function delete(Product_Id $product_id);

    /**
     * Delete all products.
     *
     * @since 0.8
     * @param Product[] $products
     * @return Product[]
     */
    public function delete_all($products);

    /**
     * Delete all variants from the parent product except the given ones.
     * This method will be replaced with the specification pattern in future versions.
     *
     * @deprecated
     * @param Product[] $product_variants
     * @param Product_Id $parentProduct_Id
     */
    public function delete_all_variants_from_parent_except($product_variants, Product_Id $parentProduct_Id);

    /**
     * Find a product by the given ID.
     *
     * @since 0.8
     * @param Product_Id $product_id
     * @return null|Product
     */
    public function find_one_by_id(Product_Id $product_id);

    /**
     * Find all products.
     *
     * @since 0.8
     * @return Product[]
     */
    public function find_all();
}
