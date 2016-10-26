<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Template_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new shop template which can be stored into a database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return Shop_Template
     */
    public function create(Title $title, Name $name);
}
