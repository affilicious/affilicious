<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Batch_Update_Task implements Batch_Update_Task_Interface
{
    /**
     * The provider with the correct credentials.
     *
     * @var Provider
     */
    protected $provider;

    /**
     * The product limit for the batch update.
     * Null stands for "no limit".
     *
     * @var null|int
     */
    protected $limit;

    /**
     * The products for the batch update which have the same provider.
     *
     * @var Product[]
     */
    protected $products;

    /**
     * @since 0.7
     * @param Provider $provider
     * @param null|int $limit
     */
    public function __construct(Provider $provider, $limit = null)
    {
        $this->provider = $provider;
        $this->limit = $limit;
        $this->products = array();
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
    public function get_limit()
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_reached_limit()
    {
        if($this->limit === null) {
            return false;
        }

        $reached = count($this->products) >= $this->limit;

        return $reached;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_product(Product_Id $product_id)
    {
        return isset($this->products[$product_id->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws \RuntimeException
     */
    public function add_product(Product $product)
    {
        if(!$product->has_id() || $this->has_product($product->get_id())) {
            return;
        }

        if($this->has_reached_limit()) {
            throw new \RuntimeException(sprintf(
                'Reached the max product limit of %d.',
                $this->limit
            ));
        }

        $this->products[$product->get_id()->get_value()] = $product;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_product(Product_Id $product_id)
    {
        if(!$this->has_product($product_id)) {
            return;
        }

        unset($this->products[$product_id->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_products()
    {
        $products = array_values($this->products);

        return $products;
    }
}
