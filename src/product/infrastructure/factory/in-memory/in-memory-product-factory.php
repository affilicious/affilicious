<?php
namespace Affilicious\Product\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Product_Factory_Interface;
use Affilicious\Product\Domain\Model\Type;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Product_Factory implements Product_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title)
    {
        $name = $title->to_name();
        $product = new Product(
            $title,
            $name,
            $name->to_key(),
            Type::simple()
        );

        return $product;
    }
}
