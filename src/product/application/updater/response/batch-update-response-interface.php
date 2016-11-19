<?php
namespace Affilicious\Product\Application\Updater\Response;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Batch_Update_Response_Interface
{
    /**
     * Insert a new update response to the batch response.
     *
     * @since 0.7
     * @param Update_Response_Interface $update_response
     */
    public function insert(Update_Response_Interface $update_response);

    /**
     * Check if the batch update response is empty.
     *
     * @since 0.7
     * @return bool
     */
    public function is_empty();

    /**
     * Get the size of the batch update response by counting all sub responses.
     *
     * @since 0.7
     * @return int
     */
    public function get_size();

    /**
     * Get all update responses.
     *
     * @since 0.7
     * @return Update_Response_Interface[]
     */
    public function get_responses();
}
