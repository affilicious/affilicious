<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\Domain_Exception;
use Affilicious\Product\Domain\Model\Product_Id;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Missing_Product_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Product_Id $product_id
     */
    public function __construct(Product_Id $product_id)
    {
        parent::__construct(sprintf(
            'The product #%s is missing in the database.',
            $product_id
        ));
    }
}
