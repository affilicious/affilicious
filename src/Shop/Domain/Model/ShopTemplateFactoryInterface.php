<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ShopTemplateFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new shop template which can be stored into a database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return ShopTemplate
     */
    public function create(Title $title, Name $name);
}
