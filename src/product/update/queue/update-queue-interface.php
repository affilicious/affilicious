<?php
namespace Affilicious\Product\Update\Queue;

use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Update_Task;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Queue_Interface
{
    const MIN = 1;
    const MAX = 100;

    /**
     * Create a new queue with the given name
     *
     * @since 0.9
     * @param string $name
     */
    public function __construct($name);

    /**
     * Get the name of the queue.
     *
     * @since 0.9
     * @return string
     */
    public function get_name();

    /**
     * Put a new update task into the queue.
     *
     * @since 0.7
     * @param Update_Task $update_task
     */
    public function put(Update_Task $update_task);

    /**
     * Put a new batch update task into the queue.
     *
     * @since 0.9
     * @param Batch_Update_Task $batch_update_task
     */
    public function put_batched(Batch_Update_Task $batch_update_task);

    /**
     * Get a one or more update tasks from the queue.
     *
     * Note that the providers often just allow a specific number of tasks/requests per second to restrict massive uncontrolled updates.
     * Please check the provider guidelines and specifications for more information.
     *
     * @since 0.7
     * @param int $number The number of update tasks to retrieve.
     * @return Update_Task[]|\WP_Error
     */
    public function get($number = 1);

    /**
     * Get one or more update tasks as one batch update task.
     *
     * Note that the providers often just allow a specific number of tasks/requests per second to restrict massive uncontrolled updates.
     * Please check the provider guidelines and specifications for more information.
     *
     * @since 0.9
     * @param int $number The number of update tasks to retrieve as one batch update task.
     * @return null|Batch_Update_Task|\WP_Error
     */
    public function get_batched($number = 10);

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
