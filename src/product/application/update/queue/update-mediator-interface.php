<?php
namespace Affilicious\Product\Application\Update\Queue;

use Affilicious\Product\Application\Update\Task\Update_Task_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Mediator_Interface
{
    /**
     * Handle the update task by putting it into the right queue.
     *
     * @since 0.7
     * @param Update_Task_Interface $update_task
     */
    public function mediate(Update_Task_Interface $update_task);

    /**
     * Check if the queue with the name already exists.
     *
     * @sine 0.7
     * @param $name
     * @return bool
     */
    public function has_queue($name);

    /**
     * Add a queue to the mediator.
     *
     * @since 0.7
     * @param Update_Queue_Interface $queue
     */
    public function add_queue(Update_Queue_Interface $queue);

    /**
     * Remove the queue by the name from the mediator.
     *
     * @since 0.7
     * @param $name
     */
    public function remove_queue($name);

    /**
     * Get all queues from the mediator.
     *
     * @since 0.7
     * @return Update_Queue_Interface[]
     */
    public function get_queues();
}
