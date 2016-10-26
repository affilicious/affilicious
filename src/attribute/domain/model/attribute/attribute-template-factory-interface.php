<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Attribute_Template_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new attribute template which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @return Attribute_Template
     */
    public function create(Title $title, Type $type);
}
