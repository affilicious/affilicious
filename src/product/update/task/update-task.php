<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
class Update_Task
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @since 0.7
     * @param Provider $provider The provider used for the task.
     * @param Product $product The product used for the task.
     */
    public function __construct(Provider $provider, Product $product)
    {
        $this->provider = $provider;
        $this->product = $product;
    }

    /**
     * Get the provider for the next update.
     *
     * @since 0.7
     * @return Provider The provider used for the task.
     */
    public function get_provider()
    {
        return $this->provider;
    }

    /**
     * Get the product for the next update.
     *
     * @since 0.7
     * @return Product The product used for the task.
     */
    public function get_product()
    {
        return $this->product;
    }
}
