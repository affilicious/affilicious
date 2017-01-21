<?php
namespace Affilicious\Product\Factory\In_Memory;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Model\Variant\Product_Variant;
use Affilicious\Product\Model\Variant\Product_Variant_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Product_Variant_Factory implements Product_Variant_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Complex_Product_Interface $parent, Name $title, Slug $name, Key $key)
    {
        $product = new Product_Variant($parent, $title, $name, $key);

        return $product;
    }
}
