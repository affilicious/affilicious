<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Simple\Simple_Product;
use Affilicious\Product\Model\Simple\Simple_Product_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Simple_Product_Factory implements Simple_Product_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Name $title, Slug $name, Key $key)
    {
        $product = new Simple_Product($title, $name, $key);

        return $product;
    }
}
