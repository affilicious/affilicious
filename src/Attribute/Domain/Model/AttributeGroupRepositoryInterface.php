<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a attribute group by the given ID
     *
     * @since 0.6
     * @param AttributeGroupId $attributeGroupId
     * @return AttributeGroup|null
     */
    public function findById(AttributeGroupId $attributeGroupId);

    /**
     * Find all attribute groups
     *
     * @since 0.6
     * @return AttributeGroup[]
     */
    public function findAll();
}
