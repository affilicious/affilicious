<?php
namespace Affilicious\ProductsPlugin\Common\Domain\Model;

interface RepositoryInterface
{
    /**
     * Find all entities
     * @return array[]
     */
    public function findAll();
}
