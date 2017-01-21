<?php
namespace Affilicious\Product\Exception;

use Affilicious\Common\Exception\Domain_Exception;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Variant\Product_Variant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Variant_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Product_Variant $product_variant
     * @param Product $product
     */
    public function __construct(Product_Variant $product_variant, Product $product)
    {
        parent::__construct(sprintf(
            'The variant #%s (%s) does already exist in the product #%s (%s)',
            $product_variant->get_id()->get_value(),
            $product_variant->get_title()->get_value(),
            $product->get_id()->get_value(),
            $product->get_title()->get_value()
        ));
    }
}
