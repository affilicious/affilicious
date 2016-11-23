<?php
namespace Affilicious\Product\Application\Update\Queue;

use Affilicious\Product\Application\Update\Task\Update_Task_Interface;

if(!defined('ABSPATH')) {
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
        $shop = $update_task->get_shop();
        $template = $shop->get_template();
        $provider = $template->get_provider();
        $name = $provider->get_name()->get_value();

        foreach ($this->queues as $queue_name => $queue){
            if($queue_name === $name) {
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
        $this->queues[$queue->get_name()] = $queue;
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
    public function get_queues()
    {
        $queues = array_values($this->queues);

        return $queues;
    }
}
