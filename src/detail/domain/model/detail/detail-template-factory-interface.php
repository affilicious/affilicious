<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Template_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new detail template which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @return Detail_Template
     */
    public function create(Title $title, Type $type);
}
