<?php
namespace Affilicious\Product\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Variant\Product_Variant;
use Affilicious\Product\Domain\Model\Variant\Product_Variant_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Product_Variant_Factory implements Product_Variant_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Complex_Product_Interface $parent, Title $title, Name $name, Key $key)
    {
        $product = new Product_Variant($parent, $title, $name, $key);

        return $product;
    }
}
