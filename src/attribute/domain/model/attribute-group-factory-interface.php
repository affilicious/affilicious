<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Attribute_Group_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new attribute group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Attribute_Group
     */
    public function create(Title $title, Name $name, Key $key);

    /**
     * Create a new attribute group from the template.
     *
     * @since 0.6
     * @param Attribute_Template_Group_Id $attribute_template_group_id
     * @param mixed $data _the structure of the data varies and depends on the implementation
     * @param Attribute_Group
     */
    public function create_from_template_id_and_data(Attribute_Template_Group_Id $attribute_template_group_id, $data);
}
