<?php
namespace Affilicious\Shop\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Shop_Template;
use Affilicious\Shop\Domain\Model\Shop_Template_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Shop_Template_Factory implements Shop_Template_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name)
    {
        $shop = new Shop_template(
            $title,
            $name,
            $name->to_key()
        );

        return $shop;
    }
}
