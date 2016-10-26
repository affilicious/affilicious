<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Repository_Interface;
use Affilicious\Product\Domain\Exception\Failed_To_Delete_Product_Exception;
use Affilicious\Product\Domain\Exception\Product_Not_Found_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Repository_Interface extends Repository_Interface
{
    /**
     * Store the product
     *
     * @since 0.6
     * @param Product $product
     * @return Product
     */
    public function store(Product $product);

    /**
     * Store all products
     *
     * @param $products
     * @return Product[]
     */
    public function store_all($products);

    /**
     * Delete the product
     *
     * @since 0.6
     * @param Product_Id $product_id
     * @return Product
     * @throws Product_Not_Found_Exception
     * @throws Invalid_Post_Type_Exception
     * @throws Failed_To_Delete_Product_Exception
     */
    public function delete(Product_Id $product_id);

    /**
     * Delete all products
     *
     * @param Product[] $products
     * @return Product[]
     * @throws Product_Not_Found_Exception
     * @throws Invalid_Post_Type_Exception
     * @throws Failed_To_Delete_Product_Exception
     */
    public function delete_all($products);

    /**
     * Delete all variants from the parent product except the given ones.
     * This method will be replaced with the specification pattern in future versions
     *
     * @deprecated
     * @param Product[] $product_variants
     * @param Product_Id $parentProduct_Id
     * @throw Invalid_Type_Exception
     */
    public function delete_all_variants_from_parent_except($product_variants, Product_Id $parentProduct_Id);

    /**
     * Find a product by the given ID
     *
     * @since 0.3
     * @param Product_Id $product_id
     * @return null|Product
     */
    public function find_by_id(Product_Id $product_id);

    /**
     * Find all products
     *
     * @since 0.3
     * @return Product[]
     */
    public function find_all();
}
