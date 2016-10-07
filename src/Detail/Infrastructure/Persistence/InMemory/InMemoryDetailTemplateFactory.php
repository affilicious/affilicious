<?php
namespace Affilicious\Detail\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplate;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplateFactoryInterface;
use Affilicious\Detail\Domain\Model\DetailTemplate\Type;

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
        $detailTemplate = new DetailTemplate(
            $title,
            $title->toKey(),
            $type
        );

        return $detailTemplate;
    }
}
