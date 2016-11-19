<?php
namespace Affilicious\Product\Application\Updater\Response;

use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Response_Interface
{
    /**
     * @since 0.7
     * @param Product_Interface $product
     * @param Shop_Interface $shop
     */
    public function __construct(Product_Interface $product, Shop_Interface $shop);

    /**
     * Get the product for the last update.
     *
     * @since 0.7
     * @return Product_Interface
     */
    public function get_product();

    /**
     * Get the shop for the last update.
     *
     * @since 0.7
     * @return Shop_Interface
     */
    public function get_shop();
}
