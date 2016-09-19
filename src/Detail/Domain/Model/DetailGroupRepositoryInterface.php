<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a field group by the given ID
     *
     * @since 0.3
     * @param DetailGroupId $detailGroupId
     * @return DetailGroup|null
     */
    public function findById(DetailGroupId $detailGroupId);

    /**
     * Find all field groups
     *
     * @since 0.3
     * @return DetailGroup[]
     */
    public function findAll();
}
