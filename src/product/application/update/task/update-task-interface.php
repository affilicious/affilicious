<?php
namespace Affilicious\Product\Application\Update\Task;

use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Task_Interface
{
    /**
     * @since 0.7
     * @param Provider_Interface $provider
     * @param Product_Interface $product
     */
    public function __construct(Provider_Interface $provider, Product_Interface $product);

    /**
     * Get the provider for the next update.
     *
     * @since 0.7
     * @return Provider_Interface
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
