<?php
namespace Affilicious\Product\Update\Task\Broker;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Broker\Queue\Update_Task_Queue;
use Affilicious\Product\Update\Task\Update_Task;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.21
 */
final class Update_Task_Broker
{
	/**
	 * @since 0.9.21
	 * @param int
	 */
	const DEFAULT_BATCH_SIZE = 10;

	/**
	 * @since 0.9.21
	 * @param int
	 */
	const DEFAULT_LIMIT = 1;

	/**
	 * @since 0.9.21
	 * @var Update_Task_Queue[]
	 */
	private $queues;

	/**
	 * @since 0.9.21
	 * @var Shop_Template_Repository_Interface
	 */
	private $shop_template_repository;

	/**
	 * @since 0.9.21
	 * @var Provider_Repository_Interface
	 */
	private $provider_repository;

	/**
	 * @since 0.9.21
	 * @param Shop_Template_Repository_Interface $shop_template_repository
	 * @param Provider_Repository_Interface $provider_repository
	 */
	public function __construct(Shop_Template_Repository_Interface $shop_template_repository, Provider_Repository_Interface $provider_repository)
	{
		$this->shop_template_repository = $shop_template_repository;
		$this->provider_repository = $provider_repository;
	}

	/**
	 * Produce one or more update tasks for the product.
	 *
	 * @since 0.9.21
	 * @param Product $product The product which will be split into tasks.
	 */
	public function produce_tasks(Product $product)
	{
		// For simple products: Just build one task for each shop.
		if($product instanceof Simple_Product) {
			$shops = $product->get_shops();
			foreach ($shops as $shop) {
				$this->put_as_task_into_queue($product, $shop);
			}
		}

		// For complex products: One task for the complex product and one or more tasks for each product variant.
		if ($product instanceof Complex_Product) {
			$default_variant = $product->get_default_variant();
			if($default_variant === null) {
				return;
			}

			// Mediate the complex product (take the first shop from the default variant).
			$shops = $default_variant->get_shops();
			if(!empty($shops[0])) {
				$this->put_as_task_into_queue($product, $shops[0]);
			}

			// Mediate the product variants
			$variants = $product->get_variants();
			foreach ($variants as $variant) {
				$shops = $variant->get_shops();
				foreach ($shops as $shop) {
					$this->put_as_task_into_queue($variant, $shop);
				}
			}
		}
	}

	/**
	 * Consume one or more update tasks for the provider.
	 *
	 * @since 0.9.21
	 * @param Provider $provider The provider for determining the right queue to get the update tasks from.
	 * @param int $limit Optional. The maximal number of tasks to consume. It's no guaranteed to get the desired number of tasks (e.g. no more tasks left).
	 * @return Update_Task[] The update tasks for the provider.
	 */
	public function consume_tasks(Provider $provider, $limit = self::DEFAULT_LIMIT)
	{
		$update_tasks = [];

		foreach ($this->queues as $queue) {
			if($this->is_queue_responsible_for_provider($queue, $provider)) {
				$update_tasks = $queue->get($limit);
				break;
			}
		}

		return $update_tasks;
	}

	/**
	 * Consume one or mote batch update tasks with the given batch size for the provider.
	 *
	 * @since 0.9.21
	 * @param Provider $provider The provider for determining the right queue to get the update tasks from.
	 * @param int $batch_size Optional. The size of the batch task. It's no guaranteed to get the desired size of tasks (e.g. no more tasks left).
	 * @param int $limit Optional. The maximal number of tasks to consume. It's no guaranteed to get the desired number of tasks (e.g. no more tasks left).
	 * @return Batch_Update_Task[] The batch update tasks for the provider.
	 */
	public function consume_batched_tasks(Provider $provider, $batch_size = self::DEFAULT_BATCH_SIZE, $limit = self::DEFAULT_LIMIT)
	{
		$batch_update_tasks = [];

		foreach ($this->queues as $queue) {
			if($this->is_queue_responsible_for_provider($queue, $provider)) {
				$batch_update_tasks = $queue->get_batched($batch_size, $limit);
			}
		}

		return $batch_update_tasks;
	}

	/**
	 * Check if the update queue with the name already exists in the broker.
	 *
	 * @since 0.9.21
	 * @param Slug $provider_slug The provider slug to identify the queue.
	 * @return bool Whether the update queue is existing or not.
	 */
	public function has_queue(Slug $provider_slug)
	{
		return isset($this->queues[$provider_slug->get_value()]);
	}

	/**
	 * Add a update queue to the broker.
	 *
	 * @since 0.9.21
	 * @param Update_Task_Queue $queue The update queue to add.
	 */
	public function add_queue(Update_Task_Queue $queue)
	{
		$this->queues[$queue->get_provider_slug()->get_value()] = $queue;
	}

	/**
	 * Remove the update queue by the name from the broker.
	 *
	 * @since 0.9.21
	 * @param Slug $provider_slug The provider slug to identify the queue.
	 */
	public function remove_queue(Slug $provider_slug)
	{
		unset($this->queues[$provider_slug->get_value()]);
	}

	/**
	 * Get the queue by the name from the broker.
	 *
	 * @since 0.9.21
	 * @param Slug $provider_slug The provider slug to identify the queue.
	 * @return Update_Task_Queue|null
	 */
	public function get_queue(Slug $provider_slug)
	{
		return isset($this->queues[$provider_slug->get_value()]) ? $this->queues[$provider_slug->get_value()] : null;
	}

	/**
	 * Get all queues from the broker.
	 *
	 * @since 0.9.21
	 * @return Update_Task_Queue[]
	 */
	public function get_queues()
	{
		return array_values($this->queues);
	}

	/**
	 * Find the provider by the given shop.
	 *
	 * @since 0.9.21
	 * @param Shop $shop
	 * @return Provider|null
	 */
	private function find_provider_by_shop(Shop $shop)
	{
		// Get the shop template ID from the shop.
		$shop_template_id = $shop->get_template_id();
		if($shop_template_id === null) {
			return null;
		}

		// Find the shop template by the ID.
		$shop_template = $this->shop_template_repository->find($shop_template_id);
		if($shop_template === null) {
			return null;
		}

		// Get the provider ID from the shop template.
		$provider_id = $shop_template->get_provider_id();
		if($provider_id === null) {
			return null;
		}

		// Find the provider by the ID.
		$provider = $this->provider_repository->find($provider_id);
		if($provider === null) {
			return null;
		}

		return $provider;
	}

	/**
	 * Put the product and shop as task into the right queue.
	 *
	 * @since 0.9.21
	 * @param Product $product
	 * @param Shop $shop
	 * @return Update_Task|null
	 */
	private function put_as_task_into_queue(Product $product, Shop $shop)
	{
		// Find the provider for the update task.
		$provider = $this->find_provider_by_shop($shop);
		if($provider === null) {
			return null;
		}

		// Build the update task from the provider and product.
		$update_task = new Update_Task($provider, $product);
		$provider_slug = $provider->get_slug()->get_value();

		// Find the right queue by the provider slug to put the update task into.
		foreach ($this->queues as $queue_slug => $queue){
			if($queue_slug === $provider_slug) {
				$queue->put($update_task);
				break;
			}
		}

		return $update_task;
	}

	/**
	 * Check if the update queue is responsible for the provider.
	 *
	 * @since 0.9.21
	 * @param Update_Task_Queue $queue
	 * @param Provider $provider
	 * @return bool
	 */
	private function is_queue_responsible_for_provider(Update_Task_Queue $queue, Provider $provider)
	{
		$matches_provider_slug = $provider->get_slug()->is_equal_to($queue->get_provider_slug());
		$matches_provider_type = $provider->get_type() !== null ? $provider->get_type()->is_equal_to($queue->get_provider_type()) : false;
		$is_responsible = $matches_provider_slug || $matches_provider_type;

		return $is_responsible;
	}
}
