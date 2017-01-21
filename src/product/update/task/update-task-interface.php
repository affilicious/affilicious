<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product_Interface;
use Affilicious\Provider\Model\Provider;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Task_Interface
{
    /**
     * @since 0.7
     * @param Provider $provider
     * @param Product_Interface $product
     */
    public function __construct(Provider $provider, Product_Interface $product);

    /**
     * Get the provider for the next update.
     *
     * @since 0.7
     * @return Provider
     */
    public function get_provider();

    /**
     * Get the product for the next update.
     *
     * @since 0.7
     * @return Product_Interface
     */
    public function get_product();
}
