<?php
namespace Affilicious\Product\Application\Update\Task;

use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Task implements Update_Task_Interface
{
    /**
     * @var Provider_Interface
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
    public function __construct(Provider_Interface $provider, Product_Interface $product)
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
