<?php
namespace Affilicious\Product\Update\Worker;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Update\Configuration\Configuration_Interface;
use Affilicious\Product\Update\Task\Batch_Update_Task_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Worker_Interface
{
    /**
     * @since 0.7
     * @param Slug $name
     */
    public function __construct(Slug $name);

    /**
     * Get the unique name of the worker.
     *
     * @since 0.7
     * @return Slug
     */
    public function get_name();

    /**
     * Configure the worker for the update.
     *
     * @since 0.7
     * @return Configuration_Interface
     */
    public function configure();

    /**
     * Execute the update tasks.
     *
     * @since 0.7
     * @param Batch_Update_Task_Interface $batch_update_task
     * @param string $update_interval
     */
    public function execute(Batch_Update_Task_Interface $batch_update_task, $update_interval);
}
