<?php
namespace Affilicious\Product\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Repository_Interface
{
    /**
     * Store the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @return Product_Interface
     */
    public function store(Product_Interface $product);

    /**
     * Store all products.
     *
     * @since 0.6
     * @param $products
     * @return Product_Interface[]
     */
    public function store_all($products);

    /**
     * Delete the product.
     *
     * @since 0.6
     * @param Product_Id $product_id
     * @return Product_Interface
     */
    public function delete(Product_Id $product_id);

    /**
     * Delete all products.
     *
     * @since 0.6
     * @param Product_Interface[] $products
     * @return Product_Interface[]
     */
    public function delete_all($products);

    /**
     * Delete all variants from the parent product except the given ones.
     * This method will be replaced with the specification pattern in future versions.
     *
     * @deprecated
     * @param Product_Interface[] $product_variants
     * @param Product_Id $parentProduct_Id
     */
    public function delete_all_variants_from_parent_except($product_variants, Product_Id $parentProduct_Id);

    /**
     * Find a product by the given ID.
     *
     * @since 0.6
     * @param Product_Id $product_id
     * @return null|Product_Interface
     */
    public function find_by_id(Product_Id $product_id);

    /**
     * Find all products.
     *
     * @since 0.6
     * @return Product_Interface[]
     */
    public function find_all();
}
