<?php
namespace Affilicious\Product\Application\Updater\Request;

use Affilicious\Product\Application\Updater\Update_Queue_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Request_Queue_Interface extends Update_Queue_Interface
{
    const MIN = 1;
    const MAX = 100;

    /**
     * Put a new update request into the queue.
     *
     * @since 0.7
     * @param Update_Request_Interface $update_request
     */
    public function put(Update_Request_Interface $update_request);

    /**
     * Get the given number of update requests from the queue as a combined batch update request.
     * The number has to be between 1 and 100.
     *
     * @since 0.7
     * @param int $number
     * @return Batch_Update_Request_Interface
     */
    public function get($number = 1);

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_size();

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_empty();
}
