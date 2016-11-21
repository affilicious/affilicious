<?php
namespace Affilicious\Product\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Simple\Simple_Product;
use Affilicious\Product\Domain\Model\Simple\Simple_Product_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Simple_Product_Factory implements Simple_Product_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Title $title, Name $name, Key $key)
    {
        $product = new Simple_Product($title, $name, $key);

        return $product;
    }
}
