<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\InMemory;

use Affilicious\Attribute\Domain\Model\AttributeTemplate\AttributeTemplate;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\AttributeTemplateFactoryInterface;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\Type;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryAttributeTemplateFactory implements AttributeTemplateFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type)
    {
        $attributeTemplate = new AttributeTemplate(
            $title,
            $title->toName(),
            $title->toKey(),
            $type
        );

        return $attributeTemplate;
    }
}
