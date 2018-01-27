<?php
namespace Affilicious\Product\Update;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Broker\Update_Task_Broker;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Type;
use Affilicious\Provider\Repository\Provider_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
final class Update_Manager
{
	/**
	 * @since 0.9
	 * @var Update_Worker_Interface[]
	 */
	private $workers = [];

	/**
	 * @since 0.9.21
	 * @var Update_Task_Broker
	 */
	private $update_task_broker;

    /**
     * @since 0.9
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @since 0.9
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

	/**
	 * @since 0.9.21
	 * @param Update_Task_Broker $update_task_broker
	 * @param Product_Repository_Interface $product_repository
	 * @param Provider_Repository_Interface $provider_repository
	 */
    public function __construct(
    	Update_Task_Broker $update_task_broker,
        Product_Repository_Interface $product_repository,
        Provider_Repository_Interface $provider_repository
    ) {
	    $this->update_task_broker = $update_task_broker;
	    $this->product_repository = $product_repository;
	    $this->provider_repository = $provider_repository;
    }

    /**
     * Run the tasks for the given update interval like hourly, twice daily or daily.
     *
     * @since 0.7
     * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
     */
    public function run_tasks($update_interval)
    {
        do_action('aff_product_update_manager_before_run_tasks', $update_interval);

        // Prepare the update tasks.
        $this->prepare_update_tasks();

        // Loop through the providers to update all related shops.
        $providers = $this->provider_repository->find_all();
        foreach ($providers as $provider) {
        	// Find the update worker which is responsible for the provider.
        	$worker = $this->find_update_worker($provider);
        	if($worker === null) {
        		continue;
	        }

	        // Find and execute the batch update tasks for the worker.
	        $batch_update_tasks = $this->find_batch_update_tasks($worker, $provider);
	        foreach ($batch_update_tasks as $batch_update_task) {
				$this->execute_batch_update_task($batch_update_task, $worker, $provider, $update_interval);
	        }
        }

	    do_action('aff_product_update_manager_after_run_tasks', $update_interval);
    }

    /**
     * Check by name if the worker exists in the manager.
     *
     * @since 0.9
     * @param string $name The name of the update worker.
     * @return bool Whether the update worker is existing or not.
     */
    public function has_worker($name)
    {
        return isset($this->workers[$name]);
    }

    /**
     * Add a new update worker to the manager.
     *
     * @since 0.9
     * @param Update_Worker_Interface $worker The update worker to add.
     */
    public function add_worker(Update_Worker_Interface $worker)
    {
        $this->workers[$worker->get_name()] = $worker;
    }

    /**
     * Remove an existing update worker by the name from the manager.
     *
     * @since 0.9
     * @param string $name The name of the update worker.
     */
    public function remove_worker($name)
    {
        unset($this->workers[$name]);
    }

    /**
     * Get the update worker by the name from the manager.
     *
     * @since 0.9
     * @param string $name The name of the update worker.
     * @return null|Update_Worker_Interface
     */
    public function get_worker($name)
    {
        if(!$this->has_worker($name)) {
            return null;
        }

        $worker = $this->workers[$name];

        return $worker;
    }

    /**
     * Get all update workers from the manager.
     *
     * @since 0.9
     * @return Update_Worker_Interface[]
     */
    public function get_workers()
    {
        $workers = array_values($this->workers);

        return $workers;
    }

	/**
	 * Prepare the update tasks for the workers.
	 *
	 * @since 0.9.21
	 */
    private function prepare_update_tasks()
    {
    	$products = $this->product_repository->find_all();
    	foreach ($products as $product) {
    		$this->update_task_broker->produce_tasks($product);
	    }
    }

	/**
	 * Find the update worker which is responsible for the provider.
	 *
	 * @since 0.9.21
	 * @param Provider $provider
	 * @return Update_Worker_Interface|null
	 */
    private function find_update_worker(Provider $provider)
    {
    	foreach ($this->workers as $worker) {
		    // Get the worker configuration
		    $configuration = new Configuration();
		    $worker->configure($configuration);

		    // Find the provider slug and type from the configuration.
		    $provider_slug = $configuration->has('provider_slug') ? new Slug($configuration->get('provider_slug')) : null;
		    $provider_type = $configuration->has('provider_type') ? new Type($configuration->get('provider_type')) : null;

		    // @deprecated 1.1 The config "provider" is deprecated. Use "provider_slug" instead.
		    $provider_slug = $provider_slug === null && $configuration->has('provider') ? new Slug($configuration->get('provider')) : $provider_slug;

		    // Check if the worker belongs to the provider.
		    $matches_provider_slug = $provider_slug !== null ? $provider_slug->is_equal_to($provider->get_slug()) : false;
		    $matches_provider_type = $provider_slug === null && $provider_type !== null ? $provider_type->is_equal_to($provider->get_type()) : false;
		    if($matches_provider_slug || $matches_provider_type) {
		    	return $worker;
		    }
	    }

	    return null;
    }

	/**
	 * Find the batch update tasks for the worker and provider.
	 *
	 * @since 0.9.21
	 * @param Update_Worker_Interface $worker
	 * @param Provider $provider
	 * @return Batch_Update_Task[]
	 */
	private function find_batch_update_tasks(Update_Worker_Interface $worker, Provider $provider)
	{
		// Get the worker configuration
		$configuration = new Configuration();
		$worker->configure($configuration);

		$batch_updates = $this->update_task_broker->consume_batched_tasks($provider, 10, 10);

		return $batch_updates;
	}

	/**
	 * @since 0.9.21
	 * @param Batch_Update_Task $batch_update_task
	 * @param Update_Worker_Interface $update_worker
	 * @param Provider $provider
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 */
	private function execute_batch_update_task(Batch_Update_Task $batch_update_task, Update_Worker_Interface $update_worker, Provider $provider, $update_interval)
	{
		do_action("aff_product_{$update_worker->get_name()}_update_worker_before_execute", $update_worker, $batch_update_task, $provider, $update_interval);
		do_action("aff_product_update_worker_before_execute", $update_worker, $batch_update_task, $provider, $update_interval);

		$update_worker->execute($batch_update_task, $update_interval);

		do_action("aff_product_{$update_worker->get_name()}_update_worker_after_execute", $update_worker, $batch_update_task, $provider, $update_interval);
		do_action("aff_product_update_worker_after_execute", $update_worker, $batch_update_task, $provider, $update_interval);
	}
}
