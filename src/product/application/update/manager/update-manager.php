<?php
namespace Affilicious\Product\Application\Update\Manager;

use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Worker\Update_Worker_Interface;

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
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Update_Mediator_Interface $mediator)
    {
        $this->mediator = $mediator;
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
     * @since 0.7
     * @param string $recurrence
     */
    public function run_tasks($recurrence)
    {

    }

    public function prepare_tasks()
    {

    }
}
