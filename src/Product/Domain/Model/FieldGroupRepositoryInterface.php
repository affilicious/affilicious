<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

use Affilicious\ProductsPlugin\Common\Domain\Model\RepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface FieldGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a field group by the given ID
     * @param int $fieldGroupId
     * @return FieldGroup|null
     */
    public function findById($fieldGroupId);

    /**
     * Find all field groups
     * @return FieldGroup[]
     */
    public function findAll();
}
