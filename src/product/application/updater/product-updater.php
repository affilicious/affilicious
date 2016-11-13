<?php
namespace Affilicious\Shop\Application\Updater;

use Affilicious\Common\Application\Queue\Min_Priority_Queue;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Updater
{
    /**
     * @var Amazon_Provider_Interface[]
     */
    protected $providers;

    /**
     * @var Min_Priority_Queue
     */
    protected $minPriorityQueue;

    /**
     * @since 0.7
     */
    public function __construct()
    {
        $this->minPriorityQueue = new Min_Priority_Queue();
    }

    /**
     * @param Product_Interface $product
     * @return Product_Interface|mixed|void
     */
    public function update(Product_Interface $product)
    {
        do_action('affilicious_product_updater_before_update', $product);
        $product = apply_filters('affilicious_updater_product_update', $product);
        do_action('affilicious_product_updater_after_update', $product);

        return $product;
    }

    /**
     * @since 0.7
     * @param Product_Interface $product
     */
    public function queue(Product_Interface $product)
    {
        $updated_at = $product->get_updated_at()->getTimestamp();
        $this->minPriorityQueue->insert($product, $updated_at);
    }








}