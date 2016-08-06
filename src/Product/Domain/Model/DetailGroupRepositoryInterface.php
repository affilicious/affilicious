<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

use Affilicious\ProductsPlugin\Common\Domain\Model\RepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface DetailGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a field group by the given ID
     * @param int $detailGroupId
     * @return DetailGroup|null
     */
    public function findById($detailGroupId);

    /**
     * Find all field groups
     * @return DetailGroup[]
     */
    public function findAll();
}
