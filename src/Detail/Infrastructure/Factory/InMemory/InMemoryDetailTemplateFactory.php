<?php
namespace Affilicious\Detail\Infrastructure\Factory\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail\DetailTemplate;
use Affilicious\Detail\Domain\Model\Detail\DetailTemplateFactoryInterface;
use Affilicious\Detail\Domain\Model\Detail\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryDetailTemplateFactory implements DetailTemplateFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type)
    {
        $name = $title->toName();
        $detailTemplate = new DetailTemplate(
            $title,
            $name,
            $name->toKey(),
            $type
        );

        return $detailTemplate;
    }
}
