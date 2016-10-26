<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Attribute_Template_Group_Repository_Interface extends Repository_Interface
{
    /**
     * Find a attribute template group by the given ID
     *
     * @since 0.6
     * @param Attribute_Template_Group_Id $attribute_group_id
     * @return null|Attribute_Template_Group
     * @throws Invalid_Post_Type_Exception
     */
    public function find_by_id(Attribute_Template_Group_Id $attribute_group_id);

    /**
     * Find all attribute template groups
     *
     * @since 0.6
     * @return Attribute_Template_Group[]
     */
    public function find_all();
}
