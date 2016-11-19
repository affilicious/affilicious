<?php
namespace Affilicious\Product\Application\Updater;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Queue_Interface
{
    /**
     * Create a new queue with the given name
     *
     * @since 0.7
     * @param string $name
     */
    public function __construct($name);

    /**
     * Get the name of the queue.
     *
     * @since 0.7
     * @return string
     */
    public function get_name();

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
