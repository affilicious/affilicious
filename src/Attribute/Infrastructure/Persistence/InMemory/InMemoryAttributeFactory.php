<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\InMemory;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\AttributeFactoryInterface;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Value;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryAttributeFactory implements AttributeFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type, Value $value)
    {
        $attribute = new Attribute(
            $title,
            $title->toKey(),
            $type,
            $value
        );

        return $attribute;
    }
}
