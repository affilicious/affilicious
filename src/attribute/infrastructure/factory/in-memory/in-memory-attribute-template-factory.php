<?php
namespace Affilicious\Attribute\Infrastructure\Factory\In_Memory;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template;
use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Attribute_Template_Factory implements Attribute_Template_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type)
    {
        $name = $title->to_name();
        $attribute_template = new Attribute_Template(
            $title,
            $name,
            $name->to_key(),
            $type
        );

        return $attribute_template;
    }
}
