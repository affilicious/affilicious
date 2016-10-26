<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Template_Group_Repository_Interface extends Repository_Interface
{
    /**
     * Find the detail template group by the given ID
     *
     * @since 0.6
     * @param Detail_Template_Group_id $detail_template_group_id
     * @return null|Detail_Template_Group
     */
    public function find_by_id(Detail_Template_Group_id $detail_template_group_id);

    /**
     * Find all detail template groups
     *
     * @since 0.6
     * @return Detail_Template_Group[]
     */
    public function find_all();
}
