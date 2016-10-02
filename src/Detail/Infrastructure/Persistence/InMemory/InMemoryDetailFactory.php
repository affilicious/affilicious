<?php
namespace Affilicious\Detail\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\DetailFactoryInterface;
use Affilicious\Detail\Domain\Model\Detail\Type;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryDetailFactory implements DetailFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Type $type)
    {
        $detail = new Detail(
            $title,
            $title->toKey(),
            $type
        );

        return $detail;
    }
}
