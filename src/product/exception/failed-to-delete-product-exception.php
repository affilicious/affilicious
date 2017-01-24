<?php
namespace Affilicious\Product\Exception;

use Affilicious\Common\Exception\Post_Not_Found_Exception;
use Affilicious\Product\Model\Product_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Failed_To_Delete_Product_Exception extends Post_Not_Found_Exception
{
    /**
     * @since 0.6
     * @param Product_Id|string|int $product_id
     */
    public function __construct($product_id)
    {
        parent::__construct(sprintf(
            "Failed to delete the product #%s",
            $product_id
        ));
    }
}
