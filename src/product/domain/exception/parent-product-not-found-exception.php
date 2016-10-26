<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;
use Affilicious\Product\Domain\Model\Product_Id;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Parent_Product_Not_Found_Exception extends Post_Not_Found_Exception
{
    /**
     * @since 0.6
     * @param Product_Id|string|int $product_id
     * @param Product_Id|string|int $product_variant_id
     */
    public function __construct($product_id, $product_variant_id)
    {
        parent::__construct(sprintf(
            "The parent product #%s for the variant #%s wasn't found.",
            $product_id,
            $product_variant_id
        ));
    }
}
