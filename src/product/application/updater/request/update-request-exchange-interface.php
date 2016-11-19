<?php
namespace Affilicious\Product\Application\Updater\Request;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Request_Exchange_Interface
{
    /**
     * Handle the update request by putting it into the right queue.
     *
     * @since 0.7
     * @param Update_Request_Interface $update_request
     */
    public function handle(Update_Request_Interface $update_request);

    /**
     * Add a queue to the exchange.
     *
     * @since 0.7
     * @param Update_Request_Queue_Interface $queue
     */
    public function add_queue(Update_Request_Queue_Interface $queue);

    /**
     * Remove the queue by the name.
     *
     * @since 0.7
     * @param $name
     */
    public function remove_queue($name);

    /**
     * Check if the queue with the name already exists.
     *
     * @sine 0.7
     * @param $name
     * @return bool
     */
    public function exists_queue($name);

    /**
     * Get all queues.
     *
     * @since 0.7
     * @return Update_Request_Queue_Interface[]
     */
    public function get_queues();

    /**
     * Count the queues.
     *
     * @since 0.7
     * @return int
     */
    public function count_queues();
}
