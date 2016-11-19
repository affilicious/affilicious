<?php
namespace Affilicious\Product\Application\Updater\Request;

use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Request implements Update_Request_Interface
{
    /**
     * @var Product_Interface
     */
    private $product;

    /**
     * @var Shop_Interface
     */
    private $shop;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Product_Interface $product, Shop_Interface $shop)
    {
        $this->product = $product;
        $this->shop = $shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_product()
    {
        return $this->product;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shop()
    {
        return $this->shop;
    }
}
