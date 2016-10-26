<?php
namespace Affilicious\Detail\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Factory_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Detail_Template_Group_Factory implements Detail_Template_Group_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $detail_template_group = new Detail_Template_Group(
            $title,
            $name,
            $name->to_key()
        );

        return $detail_template_group;
    }
}
