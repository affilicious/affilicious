<?php
namespace Affilicious\Shop\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\ShopFactoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryShopFactoryInterface implements ShopFactoryInterface
{
    /**
     * Create a new shop
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return Shop
     */
    public function create(Title $title, Name $name)
    {
        $shop = new Shop(
            $title,
            $name,
            $title->toKey()
        );

        return $shop;
    }
}
