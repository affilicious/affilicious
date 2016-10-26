<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Template_Group_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new detail template group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return Detail_Template_Group
     */
    public function create(Title $title, Name $name);
}
