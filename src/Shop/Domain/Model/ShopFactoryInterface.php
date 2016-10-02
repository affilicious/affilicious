<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ShopFactoryInterface extends FactoryInterface
{
    /**
     * Create a new shop
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return Shop
     */
    public function create(Title $title, Name $name);
}
