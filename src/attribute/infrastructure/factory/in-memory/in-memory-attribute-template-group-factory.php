<?php
namespace Affilicious\Attribute\Infrastructure\Factory\In_Memory;

use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Factory_Interface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Attribute_Template_Group_Factory implements Attribute_Template_Group_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {

        $attribute_template_group = new Attribute_Template_Group(
            $title,
            $name,
            $name->to_key()
        );

        return $attribute_template_group;
    }
}
