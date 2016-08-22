<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\RepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface DetailGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a field group by the given ID
     *
     * @since 0.3
     * @param int $detailGroupId
     * @return DetailGroup|null
     */
    public function findById($detailGroupId);

    /**
     * Find all field groups
     *
     * @since 0.3
     * @return DetailGroup[]
     */
    public function findAll();
}
