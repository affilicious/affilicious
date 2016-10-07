<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailTemplateGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find the detail template group by the given ID
     *
     * @since 0.6
     * @param DetailTemplateGroupId $detailTemplateGroupId
     * @return null|DetailTemplateGroup
     */
    public function findById(DetailTemplateGroupId $detailTemplateGroupId);

    /**
     * Find all detail template groups
     *
     * @since 0.6
     * @return DetailTemplateGroup[]
     */
    public function findAll();
}
