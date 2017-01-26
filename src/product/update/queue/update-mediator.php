<?php
namespace Affilicious\Product\Update\Queue;

use Affilicious\Product\Update\Task\Update_Task_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Mediator implements Update_Mediator_Interface
{
    /**
     * @var Update_Queue_Interface[]
     */
    protected $queues;

    /**
     * @since 0.7
     */
    public function __construct()
    {
        $this->queues = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function mediate(Update_Task_Interface $update_task)
    {
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
     * @inheritdoc
     * @since 0.7
     */
    public function has_queue($name)
    {
        return isset($this->queues[$name]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_queue(Update_Queue_Interface $queue)
    {
        $this->queues[$queue->get_slug()->get_value()] = $queue;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_queue($name)
    {
        unset($this->queues[$name]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_queue($name)
    {
        if(!$this->has_queue($name)) {
            return null;
        }

        return $this->queues[$name];
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_queues()
    {
        $queues = array_values($this->queues);

        return $queues;
    }
}
