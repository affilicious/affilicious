<?php
namespace Affilicious\Detail\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template_Factory_Interface;
use Affilicious\Detail\Domain\Model\Detail\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Detail_Template_Factory implements Detail_Template_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type)
    {
        $name = $title->to_name();
        $detail_template = new Detail_template(
            $title,
            $name,
            $name->to_key(),
            $type
        );

        return $detail_template;
    }
}
