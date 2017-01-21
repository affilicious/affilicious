<?php
namespace Affilicious\Product\Update\Manager;

use Affilicious\Product\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Affilicious\Product\Model\Product_Repository_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Manager_Interface
{
    /**
     * @since 0.7
     * @param Update_Mediator_Interface $mediator
     * @param Product_Repository_Interface $product_repository
     */
    public function __construct(Update_Mediator_Interface $mediator, Product_Repository_Interface $product_repository);

    /**
     * Check by name if the worker exists in the manager.
     *
     * @since 0.7
     * @param string $name
     * @return bool
     */
    public function has_worker($name);

    /**
     * Add a new update worker.
     *
     * @since 0.7
     * @param Update_Worker_Interface $worker
     */
    public function add_worker(Update_Worker_Interface $worker);

    /**
     * Remove an existing update worker by the name.
     *
     * @since 0.7
     * @param string $name
     */
    public function remove_worker($name);

    /**
     * Get all update workers.
     *
     * @since 0.7
     * @return Update_Worker_Interface[]
     */
    public function get_workers();

    /**
     * Set all update workers.
     *
     * @since 0.7
     * @param Update_Worker_Interface[] $workers
     */
    public function set_workers($workers);

    /**
     * Run the tasks for the given update interval like hourly, twice daily or daily.
     *
     * @since 0.7
     * @param string $update_interval
     */
    public function run_tasks($update_interval);
}
