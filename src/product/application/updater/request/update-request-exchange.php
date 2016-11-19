<?php
namespace Affilicious\Product\Application\Updater\Request;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Request_Exchange implements Update_Request_Exchange_Interface
{
    /**
     * @var Update_Request_Queue_Interface[]
     */
    protected $queues;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function handle(Update_Request_Interface $update_request)
    {
        $template = $update_request->get_shop()->get_template();
        $provider = $template->get_provider();
        $name = $provider->get_name()->get_value();

        foreach ($this->queues as $queue_name => $queue){
            if($queue_name === $name) {
                $queue->put($update_request);
                break;
            }
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_queue(Update_Request_Queue_Interface $queue)
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
    public function exists_queue($name)
    {
        return isset($this->queues[$name]);
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

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function count_queues()
    {
        return count($this->queues);
    }
}
