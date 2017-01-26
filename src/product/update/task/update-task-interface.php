<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Task_Interface
{
    /**
     * @since 0.7
     * @param Provider $provider
     * @param Product $product
     */
    public function __construct(Provider $provider, Product $product);

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
     * @return Product
     */
    public function get_product();
}
