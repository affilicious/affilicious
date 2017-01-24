<?php
namespace Affilicious\Product\Exception;

use Affilicious\Common\Exception\Post_Not_Found_Exception;
use Affilicious\Product\Model\Product_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Variant_Not_Found_Exception extends Post_Not_Found_Exception
{
    /**
     * @since 0.6
     * @param Product_Id|string|int $product_variant_id
     */
    public function __construct($product_variant_id)
    {
        parent::__construct(sprintf(
            "The product variant #%s wasn't found.",
            $product_variant_id
        ));
    }
}
