<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\RepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeTemplateGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a attribute template group by the given ID
     *
     * @since 0.6
     * @param AttributeTemplateGroupId $attributeGroupId
     * @return null|AttributeTemplateGroup
     * @throws InvalidPostTypeException
     */
    public function findById(AttributeTemplateGroupId $attributeGroupId);

    /**
     * Find all attribute template groups
     *
     * @since 0.6
     * @return AttributeTemplateGroup[]
     */
    public function findAll();
}
