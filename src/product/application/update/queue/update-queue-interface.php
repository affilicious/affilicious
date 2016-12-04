<?php
namespace Affilicious\Product\Application\Update\Queue;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Product\Application\Update\Task\Update_Task_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Queue_Interface
{
    const MIN = 1;
    const MAX = 100;

    /**
     * Create a new queue with the given name
     *
     * @since 0.7
     * @param Name $name
     */
    public function __construct(Name $name);

    /**
     * Get the name of the queue.
     *
     * @since 0.7
     * @return Name
     */
    public function get_name();

    /**
     * Put a new update task into the queue.
     *
     * @since 0.7
     * @param Update_Task_Interface $update_task
     */
    public function put(Update_Task_Interface $update_task);

    /**
     * Get a one or more update tasks from the queue.
     * You can apply this method only once in a cron request.
     *
     * Note that the providers often just allow a specific number of tasks/requests per second to restrict massive uncontrolled updates.
     * Please check the provider guidelines and specifications for more information.
     *
     * @since 0.7
     * @param int $number
     * @return Update_Task_Interface[]
     */
    public function get($number = 1);

    /**
     * Get the size of the queue.
     *
     * @since 0.7
     * @return int
     */
    public function get_size();

    /**
     * Check if the queue is empty.
     *
     * @since 0.7
     * @return bool
     */
    public function is_empty();
}
