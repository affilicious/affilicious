<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Attribute\Domain\Model\AttributeGroupFactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryAttributeGroupFactory implements AttributeGroupFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $detailGroup = new AttributeGroup(
            $title,
            $name,
            $title->toKey()
        );

        return $detailGroup;
    }
}
