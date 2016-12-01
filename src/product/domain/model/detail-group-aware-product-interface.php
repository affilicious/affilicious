<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Detail\Domain\Model\Detail_Group;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Group_Aware_Product_Interface extends Product_Interface
{
    /**
     * Check if the product has a specific detail group.
     *
     * @since 0.7
     * @param Name $name
     * @return bool
     */
    public function has_detail_group(Name $name);

    /**
     * Add a new detail group.
     *
     * @since 0.7
     * @param Detail_Group $detail_group
     */
    public function add_detail_group(Detail_Group $detail_group);

    /**
     * Remove a detail group by the name.
     *
     * @since 0.7
     * @param Name $name
     */
    public function remove_detail_group(Name $name);

    /**
     * Get a detail group by the name.
     *
     * @since 0.7
     * @param Name $name
     * @return null|Detail_Group
     */
    public function get_detail_group(Name $name);

    /**
     * Get all detail groups.
     *
     * @since 0.7
     * @return Detail_Group[]
     */
    public function get_detail_groups();

    /**
     * Set all detail groups.
     * If you do this, the old detail groups going to be replaced.
     *
     * @since 0.7
     * @param Detail_Group[] $detail_groups
     */
    public function set_detail_groups($detail_groups);
}
