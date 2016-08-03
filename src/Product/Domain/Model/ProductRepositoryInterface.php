<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

use Affilicious\ProductsPlugin\Common\Domain\Model\RepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface ProductRepositoryInterface extends RepositoryInterface
{
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
