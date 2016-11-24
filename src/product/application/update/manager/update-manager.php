<?php
namespace Affilicious\Product\Application\Update\Manager;

use Affilicious\Product\Application\Update\Configuration\Configuration_Context;
use Affilicious\Product\Application\Update\Configuration\Configuration_Resolver;
use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Queue\Update_Queue_Interface;
use Affilicious\Product\Application\Update\Task\Update_Task;
use Affilicious\Product\Application\Update\Worker\Update_Worker_Interface;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Product_Repository_Interface;
use Affilicious\Product\Domain\Model\Simple\Simple_Product_Interface;
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
        $this->workers[$worker->get_name()] = $worker;
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

            $config = $worker->configure();
            $config_context = new Configuration_Context(array('update_interval' => $update_interval));
            $config_resolver = new Configuration_Resolver($config_context);

            $update_tasks = $config_resolver->resolve($config, $queue);
            $worker->execute($update_tasks);
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
        if(count($products) == 0) {
            return;
        }

        foreach ($products as $product) {

            if($product instanceof Simple_Product_Interface || $product instanceof Complex_Product_Interface) {
                $shops = $product->get_shops();
                foreach ($shops as $shop) {
                    $this->mediate_product($product, $shop);
                }
            }

            if($product instanceof Complex_Product_Interface) {
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
     * @param Product_Interface $product
     * @param Shop_Interface $shop
     */
    private function mediate_product(Product_Interface $product, Shop_Interface $shop)
    {
        $template = $shop->get_template();
        if(!$template->has_provider()) {
            return;
        }

        $provider = $template->get_provider();
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
    private function find_worker_for_queue(Update_Queue_Interface $queue)
    {
        foreach ($this->workers as $worker) {
            $config = $worker->configure();

            if($config->get('provider') === $queue->get_name()) {
                return $worker;
            }
        }

        return null;
    }
}
