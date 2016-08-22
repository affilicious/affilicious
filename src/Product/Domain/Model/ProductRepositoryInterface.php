<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

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
