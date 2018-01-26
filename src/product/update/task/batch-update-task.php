<?php
namespace Affilicious\Product\Update\Task;

use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Batch_Update_Task
{
	/**
	 * Null stands for "no limit"
	 */
	const NO_LIMIT = null;

    /**
     * The provider with the correct credentials.
     *
     * @var Provider
     */
    private $provider;

    /**
     * The product limit for the batch update.
     * Null stands for "no limit".
     *
     * @var null|int
     */
    private $limit;

    /**
     * The products for the batch update which have the same provider.
     *
     * @var Product[]
     */
    private $products;

    /**
     * @since 0.7
     * @param Provider $provider The provider with the correct credentials.
     * @param null|int $limit The product limit for the batch update.
     */
    public function __construct(Provider $provider, $limit = self::NO_LIMIT)
    {
        $this->provider = $provider;
        $this->limit = $limit;
        $this->products = [];
    }

    /**
     * Get the provider for the next batch update.
     *
     * @since 0.7
     * @return Provider
     */
    public function get_provider()
    {
        return $this->provider;
    }

    /**
     * Get the product limit of the batch update.
     * Null stands for "no limit".
     *
     * @since 0.7
     * @return null|int
     */
    public function get_limit()
    {
        return $this->limit;
    }

    /**
     * Check of the batch update has reached the max product limit.
     *
     * @since 0.7
     * @return bool
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
     * Check if the product exists in the batch update.
     *
     * @since 0.7
     * @param Product_Id $product_id
     * @return bool
     */
    public function has_product(Product_Id $product_id)
    {
        return isset($this->products[$product_id->get_value()]);
    }

    /**
     * Add the product to the batch update.
     *
     * @since 0.7
     * @param Product $product
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
     * Remove the product from the batch update by the ID.
     *
     * @since 0.7
     * @param Product_Id $product_id
     */
    public function remove_product(Product_Id $product_id)
    {
        if(!$this->has_product($product_id)) {
            return;
        }

        unset($this->products[$product_id->get_value()]);
    }

    /**
     * Get the products for the next batch update.
     *
     * @since 0.7
     * @return Product[]
     */
    public function get_products()
    {
        $products = array_values($this->products);

        return $products;
    }
}
