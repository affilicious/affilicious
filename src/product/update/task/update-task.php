<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product_Interface;
use Affilicious\Provider\Model\Provider;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Task implements Update_Task_Interface
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var Product_Interface
     */
    protected $product;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Provider $provider, Product_Interface $product)
    {
        $this->provider = $provider;
        $this->product = $product;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_provider()
    {
        return $this->provider;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_product()
    {
        return $this->product;
    }
}
