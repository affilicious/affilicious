<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroup;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupFactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryAttributeTemplateGroupFactory implements AttributeTemplateGroupFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {

        $attributeTemplateGroup = new AttributeTemplateGroup(
            $title,
            $name,
            $name->toKey()
        );

        return $attributeTemplateGroup;
    }
}
