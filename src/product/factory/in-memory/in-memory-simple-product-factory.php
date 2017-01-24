<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Factory\Simple_Product_Factory_Interface;
use Affilicious\Product\Model\Simple_Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Simple_Product_Factory implements Simple_Product_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create(Name $name, Slug $slug)
    {
        $product = new Simple_Product($name, $slug);

        return $product;
    }
}
