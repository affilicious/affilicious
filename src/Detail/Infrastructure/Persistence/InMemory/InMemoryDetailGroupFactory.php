<?php
namespace Affilicious\Detail\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Detail\Domain\Model\DetailGroupFactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryDetailGroupFactory implements DetailGroupFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $detailGroup = new DetailGroup(
            $title,
            $name,
            $title->toKey()
        );

        return $detailGroup;
    }
}
