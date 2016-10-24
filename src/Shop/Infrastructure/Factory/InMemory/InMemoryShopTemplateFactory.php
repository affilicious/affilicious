<?php
namespace Affilicious\Shop\Infrastructure\Factory\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\ShopTemplate;
use Affilicious\Shop\Domain\Model\ShopTemplateFactoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryShopTemplateFactory implements ShopTemplateFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $shop = new ShopTemplate(
            $title,
            $name,
            $name->toKey()
        );

        return $shop;
    }
}
