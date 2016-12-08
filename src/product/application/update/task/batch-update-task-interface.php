<?php
namespace Affilicious\Product\Application\Update\Task;

use Affilicious\Product\Domain\Model\Product_Id;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Batch_Update_Task_Interface
{
    /**
     * @since 0.7
     * @param Provider_Interface $provider The provider with the correct credentials.
     * @param null|int $limit The product limit for the batch update.
     */
    public function __construct(Provider_Interface $provider, $limit = null);

    /**
     * Get the provider for the next batch update.
     *
     * @since 0.7
     * @return Provider_Interface
     */
    public function get_provider();

    /**
     * Get the product limit of the batch update.
     * Null stands for "no limit".
     *
     * @since 0.7
     * @return null|int
     */
    public function get_limit();

    /**
     * Check of the batch update has reached the max product limit.
     *
     * @since 0.7
     * @return bool
     */
    public function has_reached_limit();

    /**
     * Check if the product exists in the batch update.
     *
     * @since 0.7
     * @param Product_Id $product_id
     * @return bool
     */
    public function has_product(Product_Id $product_id);

    /**
     * Add the product to the batch update.
     *
     * @since 0.7
     * @param Product_Interface $product
     */
    public function add_product(Product_Interface $product);

    /**
     * Remove the product from the batch update by the ID.
     *
     * @since 0.7
     * @param Product_Id $product_id
     */
    public function remove_product(Product_Id $product_id);

    /**
     * Get the products for the next batch update.
     *
     * @since 0.7
     * @return Product_Interface[]
     */
    public function get_products();
}
