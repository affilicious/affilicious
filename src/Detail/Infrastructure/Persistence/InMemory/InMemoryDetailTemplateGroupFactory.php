<?php
namespace Affilicious\Detail\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupFactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryDetailTemplateGroupFactory implements DetailTemplateGroupFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $detailTemplateGroup = new DetailTemplateGroup(
            $title,
            $name,
            $title->toKey()
        );

        return $detailTemplateGroup;
    }
}
