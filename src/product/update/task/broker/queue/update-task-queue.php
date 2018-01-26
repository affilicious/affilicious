<?php
namespace Affilicious\Product\Update\Task\Broker\Queue;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Queue\Min_Priority_Queue;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Update_Task;
use Affilicious\Provider\Model\Type;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.21
 */
final class Update_Task_Queue
{
	/**
	 * @since 0.9.21
	 */
	const DEFAULT_BATCH_SIZE = 10;

	/**
	 * @since 0.9.21
	 */
	const DEFAULT_LIMIT = 1;

	/**
	 * @since 0.9.21
	 * @var Min_Priority_Queue
	 */
	private $min_priority_queue;

	/**
	 * @since 0.9.21
	 * @var Slug
	 */
	private $provider_slug;

	/**
	 * @since 0.9.21
	 * @var Type
	 */
	private $provider_type;

	/**
	 * @since 0.9.21
	 * @param Slug $provider_slug The slug of the provider used for the update task queue.
	 * @param Type|null $provider_type Optional. The of the provider used for the update task queue. This argument is optional now, but will be required in future versions. Default: null
	 */
	public function __construct(Slug $provider_slug, Type $provider_type = null)
	{
		$this->provider_slug = $provider_slug;
		$this->provider_type = $provider_type;
		$this->min_priority_queue = new Min_Priority_Queue();
	}

	/**
	 * Get the provider slug used for the update task queue.
	 *
	 * @since 0.9.21
	 * @return Slug The provider slug.
	 */
	public function get_provider_slug()
	{
		return $this->provider_slug;
	}

	/**
	 * The provider type used for the update task queue.
	 *
	 * @since 0.9.21
	 * @return Type|null The provider type might be null, but will be always type in future versions.
	 */
	public function get_provider_type()
	{
		return $this->provider_type;
	}

	/**
	 * Put a new update task into the queue.
	 *
	 * @since 0.9.21
	 * @param Update_Task $update_task The update tasks which is put into the queue.
	 */
	public function put(Update_Task $update_task)
	{
		$product = $update_task->get_product();
		$shops = $product instanceof Shop_Aware_Interface ? $product->get_shops() : [];
		$updated_at = $product->get_updated_at()->getTimestamp();

		foreach ($shops as $shop) {
			if ($shop->get_updated_at()->getTimestamp() < $updated_at) {
				$updated_at = $shop->get_updated_at()->getTimestamp();
			}
		}

		$this->min_priority_queue->insert($update_task, $updated_at);
	}

	/**
	 * Put a new batch update task into the queue.
	 *
	 * @since 0.9.21
	 * @param Batch_Update_Task $batch_update_task The batch update task which is put into the queue.
	 */
	public function put_batched(Batch_Update_Task $batch_update_task)
	{
		$provider = $batch_update_task->get_provider();
		$products = $batch_update_task->get_products();

		foreach ($products as $product) {
			$update_task = new Update_Task($provider, $product);
			$this->put($update_task);
		}
	}

	/**
	 * Get a one or more update tasks from the queue.
	 *
	 * Note that the providers often just allow a specific number of tasks/requests per second to restrict massive uncontrolled updates.
	 * Please check the provider guidelines and specifications for more information.
	 *
	 * @since 0.9.21
	 * @param int $limit Optional. The maximal number of tasks to consume. It's no guaranteed to get the desired number of tasks (e.g. no more tasks left).
	 * @return Update_Task[]
	 */
	public function get($limit = self::DEFAULT_LIMIT)
	{
		// First, check if there are any tasks left.
		if ($this->is_empty()) {
			return [];
		}

		$already_used = array();
		$result = array();
		for ($i = 0; $i < $limit; $i ++) {
			if ( ! $this->is_empty()) {
				/** @var Update_Task $update_task */
				$update_task = $this->min_priority_queue->extract();
				$product_id  = $update_task->get_product()->get_id();

				// We don't want to use the same update task twice...
				if (in_array($product_id->get_value(), $already_used)) {
					continue;
				}

				$result[]       = $update_task;
				$already_used[] = $product_id->get_value();
			}
		}

		return $result;
	}

	/**
	 * Get one or more update tasks as one batch update task.
	 *
	 * Note that the providers often just allow a specific number of tasks/requests per second to restrict massive uncontrolled updates.
	 * Please check the provider guidelines and specifications for more information.
	 *
	 * @since 0.9.21
	 * @param int $batch_size Optional. The size of the batch task. It's no guaranteed to get the desired size of tasks (e.g. no more tasks left).
	 * @param int $limit Optional. The maximal number of tasks to consume. It's no guaranteed to get the desired number of tasks (e.g. no more tasks left).
	 * @return Batch_Update_Task[] The given number of batch update tasks
	 */
	public function get_batched($batch_size = self::DEFAULT_BATCH_SIZE, $limit = self::DEFAULT_LIMIT)
	{
		$batch_update_tasks = [];

		for ($i = 0; $i < $limit; $i++) {
			$update_tasks = $this->get($batch_size);
			if (empty($update_tasks)) {
				break;
			}

			// Build one batch update task from the multiple update tasks
			$provider = $update_tasks[0]->get_provider();
			$batch_update_task = new Batch_Update_Task($provider);
			foreach ($update_tasks as $update_task) {
				$product = $update_task->get_product();
				$batch_update_task->add_product($product);
			}

			$batch_update_tasks[] = $batch_update_task;
		}

		return $batch_update_tasks;
	}

	/**
	 * Get the size of the queue.
	 *
	 * @since 0.9.21
	 * @return int
	 */
	public function get_size()
	{
		return $this->min_priority_queue->count();
	}

	/**
	 * Check if the queue is empty.
	 *
	 * @since 0.9.21
	 * @return bool
	 */
	public function is_empty()
	{
		return $this->get_size() == 0;
	}
}
