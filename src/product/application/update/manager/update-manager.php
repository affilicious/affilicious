<?php
namespace Affilicious\Product\Application\Update\Manager;

use Affilicious\Product\Application\Update\Configuration\Configuration_Context;
use Affilicious\Product\Application\Update\Configuration\Configuration_Resolver;
use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Queue\Update_Queue_Interface;
use Affilicious\Product\Application\Update\Task\Batch_Update_Task;
use Affilicious\Product\Application\Update\Task\Batch_Update_Task_Interface;
use Affilicious\Product\Application\Update\Task\Update_Task;
use Affilicious\Product\Application\Update\Task\Update_Task_Interface;
use Affilicious\Product\Application\Update\Worker\Update_Worker_Interface;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Product_Repository_Interface;
use Affilicious\Product\Domain\Model\Shop_Aware_Product_Interface;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Manager implements Update_Manager_Interface
{
    /**
     * @var Update_Mediator_Interface
     */
    protected $mediator;

    /**
     * @var Update_Worker_Interface[]
     */
    protected $workers;

    /**
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Update_Mediator_Interface $mediator, Product_Repository_Interface $product_repository)
    {
        $this->mediator = $mediator;
        $this->product_repository = $product_repository;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_worker($name)
    {
        return isset($this->workers[$name]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_worker(Update_Worker_Interface $worker)
    {
        $this->workers[$worker->get_name()->get_value()] = $worker;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_worker($name)
    {
        unset($this->workers[$name]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_workers()
    {
        $workers = array_values($this->workers);

        return $workers;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_workers($workers)
    {
        $this->workers = array();

        foreach ($workers as $worker) {
            $this->add_worker($worker);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function run_tasks($update_interval)
    {
        $this->prepare_tasks();

        $queues = $this->mediator->get_queues();
        foreach ($queues as $queue) {
            $worker = $this->find_worker_for_queue($queue);
            if($worker === null) {
                continue;
            }

            $batch_update_task = $this->get_batch_update_task($worker, $queue, $update_interval);
            if($batch_update_task === null) {
                continue;
            }

            $worker->execute($batch_update_task, $update_interval);
            $this->store_batch_update_task($batch_update_task);
        }
    }

    /**
     * Create and mediate the tasks into the right queues.
     *
     * @since 0.7
     */
    public function prepare_tasks()
    {
        $products = $this->product_repository->find_all();
        foreach ($products as $product) {
            if($product instanceof Shop_Aware_Product_Interface) {
                $shops = $product->get_shops();
                foreach ($shops as $shop) {
                    $this->mediate_product($product, $shop);
                }
            } elseif ($product instanceof Complex_Product_Interface) {
                $default_variant = $product->get_default_variant();
                if($default_variant === null) {
                    continue;
                }

                $shops = $default_variant->get_shops();
                foreach ($shops as $shop) {
                    $this->mediate_product($product, $shop);
                }
            }
        }
    }

    /**
     * Mediate the product by the shop.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param Shop_Interface $shop
     */
    protected function mediate_product(Product_Interface $product, Shop_Interface $shop)
    {
        $template = $shop->get_template();
        $provider = $template->get_provider();
        if($provider === null) {
            return;
        }

        $update_task = new Update_Task($provider, $product);
        $this->mediator->mediate($update_task);
    }

    /**
     * Find the worker for the queue.
     *
     * @since 0.7
     * @param Update_Queue_Interface $queue
     * @return null|Update_Worker_Interface
     */
    protected function find_worker_for_queue(Update_Queue_Interface $queue)
    {
        foreach ($this->workers as $worker) {
            $config = $worker->configure();

            if($config->get('provider') === $queue->get_name()->get_value()) {
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
     * @return null|Batch_Update_Task_Interface
     */
    protected function get_batch_update_task(Update_Worker_Interface $worker, Update_Queue_Interface $queue, $update_interval)
    {
        $config = $worker->configure();
        $config_context = new Configuration_Context(array('update_interval' => $update_interval));
        $config_resolver = new Configuration_Resolver($config_context);

        /** @var Update_Task_Interface[] $update_tasks */
        $update_tasks = $config_resolver->resolve($config, $queue);
        if(empty($update_tasks)) {
            return null;
        }

        $provider = $update_tasks[0]->get_provider();
        $batch_update_task = new Batch_Update_Task($provider);
        foreach ($update_tasks as $update_task) {
            $product = $update_task->get_product();
            $batch_update_task->add_product($product);
        }

        return $batch_update_task;
    }

    /**
     * Store all products of the update tasks.
     *
     * @since 0.7
     * @param Batch_Update_Task_Interface $batch_update_task
     */
    protected function store_batch_update_task(Batch_Update_Task_Interface $batch_update_task)
    {
        $products = $batch_update_task->get_products();
        $this->product_repository->store_all($products);
    }
}
