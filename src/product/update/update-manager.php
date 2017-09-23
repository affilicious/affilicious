<?php
namespace Affilicious\Product\Update;

use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Configuration\Configuration_Context;
use Affilicious\Product\Update\Configuration\Configuration_Resolver;
use Affilicious\Product\Update\Queue\Update_Queue_Interface;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Update_Task;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Update_Manager
{
    /**
     * @var Update_Worker_Interface[]
     */
    private $workers;

    /**
     * @var Update_Queue_Interface[]
     */
    private $queues;

    /**
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.9
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Product_Repository_Interface $product_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    )
    {
        $this->product_repository = $product_repository;
        $this->shop_template_repository = $shop_template_repository;
        $this->provider_repository = $provider_repository;
        $this->workers = [];
        $this->queues = [];
    }

    /**
     * Run the tasks for the given update interval like hourly, twice daily or daily.
     *
     * @since 0.7
     * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
     */
    public function run_tasks($update_interval)
    {
        do_action('aff_product_update_manager_before_run_tasks', $this);

        $this->prepare_tasks();

        foreach ($this->queues as $queue) {
            $worker = $this->find_worker_for_queue($queue);
            if($worker === null) {
                continue;
            }

            $batch_update_task = $this->get_batch_update_task($worker, $queue, $update_interval);
            if($batch_update_task === null) {
                continue;
            }

            do_action("aff_product_{$worker->get_name()}_update_worker_before_execute", $worker, $batch_update_task, $update_interval);
            do_action("aff_product_update_worker_before_execute", $worker, $batch_update_task, $update_interval);

            $worker->execute($batch_update_task, $update_interval);

            do_action("aff_product_{$worker->get_name()}_update_worker_after_execute", $worker, $batch_update_task, $update_interval);
            do_action("aff_product_update_worker_after_execute", $worker, $batch_update_task, $update_interval);
        }

        do_action('aff_product_update_manager_after_run_tasks', $this);
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
        do_action('aff_product_update_manager_before_add_worker', $worker, $this);

        $worker = apply_filters('aff_product_update_manager_add_worker', $worker, $this);
        $this->workers[$worker->get_name()] = $worker;

        do_action('aff_product_update_manager_after_add_worker', $worker, $this);
    }

    /**
     * Remove an existing update worker by the name from the manager.
     *
     * @since 0.9
     * @param string $name The name of the update worker.
     */
    public function remove_worker($name)
    {
        do_action('aff_product_update_manager_before_remove_worker', $name, $this);

        $name = apply_filters('aff_product_update_manager_remove_worker', $name, $this);
        unset($this->workers[$name]);

        do_action('aff_product_update_manager_after_remove_worker', $name, $this);
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
     * Check if the update queue with the name already exists in the manager.
     *
     * @sine 0.9
     * @param string $name The name of the update queue.
     * @return bool Whether the update queue is existing or not.
     */
    public function has_queue($name)
    {
        return isset($this->queues[$name]);
    }

    /**
     * Add a update queue to the manager.
     *
     * @since 0.9
     * @param Update_Queue_Interface $queue The update queue to add.
     */
    public function add_queue(Update_Queue_Interface $queue)
    {
        do_action('aff_product_update_manager_before_add_queue', $queue, $this);

        $queue = apply_filters('aff_product_update_manager_add_queue', $queue, $this);
        $this->queues[$queue->get_name()] = $queue;

        do_action('aff_product_update_manager_after_add_queue', $queue, $this);
    }

    /**
     * Remove the update queue by the name from the manager.
     *
     * @since 0.9
     * @param string $name The name of the update queue.
     */
    public function remove_queue($name)
    {
        do_action('aff_product_update_manager_before_remove_queue', $name, $this);

        $name = apply_filters('aff_product_update_manager_remove_queue', $name, $this);
        unset($this->queues[$name]);

        do_action('aff_product_update_manager_after_remove_queue', $name, $this);
    }

    /**
     * Get the queue by the name from the manager.
     *
     * @since 0.9
     * @param string $name The name of the update queue.
     * @return null|Update_Queue_Interface
     */
    public function get_queue($name)
    {
        if(!$this->has_queue($name)) {
            return null;
        }

        $queue = $this->queues[$name];

        return $queue;
    }

    /**
     * Get all queues from the manager.
     *
     * @since 0.9
     * @return Update_Queue_Interface[]
     */
    public function get_queues()
    {
        $queues = array_values($this->queues);

        return $queues;
    }

    /**
     * Create and mediate the tasks into the right queues.
     *
     * @since 0.7
     */
    private function prepare_tasks()
    {
        $products = $this->product_repository->find_all();

        foreach ($products as $product) {
            if($product instanceof Shop_Aware_Interface && !($product instanceof Product_Variant)) {
                $shops = $product->get_shops();
                foreach ($shops as $shop) {
                    $this->mediate_product($product, $shop);
                }
            } elseif ($product instanceof Complex_Product) {
                $default_variant = $product->get_default_variant();
                if($default_variant === null) {
                    continue;
                }

                // Mediate the complex product (take the shops from the default variant)
                $shops = $default_variant->get_shops();
                foreach ($shops as $shop) {
                    $this->mediate_product($product, $shop);
                }

                // Mediate the product variants
                $variants = $product->get_variants();
                foreach ($variants as $variant) {
                    $shops = $variant->get_shops();
                    foreach ($shops as $shop) {
                        $this->mediate_product($variant, $shop);
                    }
                }
            }
        }
    }

    /**
     * Mediate the product by the shop.
     *
     * @since 0.7
     * @param Product $product
     * @param Shop $shop
     */
    private function mediate_product(Product $product, Shop $shop)
    {
        if(!$shop->has_template_id()) {
            return;
        }

        $template = $this->shop_template_repository->find_one_by_id($shop->get_template_id());
        if($template === null) {
            return;
        }

        if(!$template->has_provider_id()) {
            return;
        }

        $provider = $this->provider_repository->find_one_by_id($template->get_provider_id());
        if($provider === null) {
            return;
        }

        $update_task = new Update_Task($provider, $product);

        $provider = $update_task->get_provider();
        $slug = $provider->get_slug()->get_value();

        foreach ($this->queues as $queue_slug => $queue){
            if($queue_slug === $slug) {
                $queue->put($update_task);
                break;
            }
        }
    }

    /**
     * Find the worker for the queue.
     *
     * @since 0.7
     * @param Update_Queue_Interface $queue
     * @return null|Update_Worker_Interface
     */
    private function find_worker_for_queue(Update_Queue_Interface $queue)
    {
        foreach ($this->workers as $worker) {
            $config = new Configuration();
            $worker->configure($config);

            // @deprecated 1.1 The config "provider" is deprecated. Use "provider_slug" instead.
            if($config->get('provider') === $queue->get_provider_slug() ||
               $config->get('provider_slug') === $queue->get_provider_slug() ||
               $config->get('provider_type') === $queue->get_provider_type()) {
                return $worker;
            }
        }

        return null;
    }

    /**
     * Find the update tasks for the worker and queue.
     *
     * @since 0.7
     * @param Update_Worker_Interface $worker
     * @param Update_Queue_Interface $queue
     * @param string $update_interval
     * @return null|Batch_Update_Task
     */
    private function get_batch_update_task(Update_Worker_Interface $worker, Update_Queue_Interface $queue, $update_interval)
    {
        $config = new Configuration();
        $worker->configure($config);

        $config_context = new Configuration_Context(array(
            'queue' => $queue,
            'update_interval' => $update_interval
        ));

        $config_resolver = new Configuration_Resolver();

        /** @var Update_Task[] $update_tasks */
        $batch_update_task = $config_resolver->resolve($config, $config_context);
        if(empty($batch_update_task) || $batch_update_task instanceof \WP_Error) {
            return null;
        }

        return $batch_update_task;
    }
}
