<?php
namespace Affilicious\Product\Application\Updater\Response;

use Affilicious\Product\Application\Updater\Update_Queue_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Response_Queue_Interface extends Update_Queue_Interface
{
    /**
     * Put a new update response into the queue.
     *
     * @since 0.7
     * @param Update_Response_Interface $update_response
     */
    public function put(Update_Response_Interface $update_response);

    /**
     * Get the given number of update responses from the queue as a combined batch update response.
     *
     * @since 0.7
     * @param int $number
     * @return Batch_Update_Response_Interface
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
