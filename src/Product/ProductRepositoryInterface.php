<?php
namespace Affilicious\ProductsPlugin\Product;

interface ProductRepositoryInterface
{
    /**
     * Store a new product
     * @param Product $product
     */
    public function store(Product $product);

    /**
     * Delete a product with the given ID
     * @param int $productId
     */
    public function delete($productId);

    /**
     * Find a product by the given ID
     * @param int $productId
     * @return Product|null
     */
    public function findById($productId);

    /**
     * Find all products
     * @return Product[]
     */
    public function findAll();

}