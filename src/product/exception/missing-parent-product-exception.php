<?php
namespace Affilicious\Product\Exception;

use Affilicious\Product\Model\Product_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Missing_Parent_Product_Exception extends \RuntimeException
{
    /**
     * @since 0.6
     * @param Product_Id $product_variant_id
     */
    public function __construct(Product_Id $product_variant_id)
    {
        parent::__construct(sprintf(
            'The parent product for the variant #%s is missing in the database.',
            $product_variant_id
        ));
    }
}
