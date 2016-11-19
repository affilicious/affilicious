<?php
namespace Affilicious\Product\Application\Updater\Request;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Batch_Update_Request_Interface
{
    /**
     * Insert a new update request to the batch request.
     *
     * @since 0.7
     * @param Update_Request_Interface $update_request
     */
    public function insert(Update_Request_Interface $update_request);

    /**
     * Check if the batch update request is empty.
     *
     * @since 0.7
     * @return bool
     */
    public function is_empty();

    /**
     * Get the size of the batch update request by counting all sub requests.
     *
     * @since 0.7
     * @return int
     */
    public function get_size();

    /**
     * Get all update requests.
     *
     * @since 0.7
     * @return Update_Request_Interface[]
     */
    public function get_requests();
}
