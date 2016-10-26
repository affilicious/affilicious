<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Group_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new detail group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @return Detail_Group
     */
    public function create(Title $title, Name $name, Key $key);

    /**
     * Create a new detail group from the template.
     *
     * @since 0.6
     * @param Detail_Template_Group_id $detail_template_group_id
     * @param mixed $data The structure of the data varies and depends on the implementation
     * @return Detail_Group
     */
    public function create_from_template_id_and_data(Detail_Template_Group_id $detail_template_group_id, $data);
}
