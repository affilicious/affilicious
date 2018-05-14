<?php
namespace Affilicious\Product\Update\Worker;

use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Task\Batch_Update_Task;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
interface Update_Worker_Interface
{
    /**
     * Get the unique name of the worker to distinguish it from the other ones.
     *
     * @since 0.7
     * @return string
     */
    public function get_name();

    /**
     * Configure the worker for the update.
     * This method basically determines the values of the batch update task of the method "execute".
     *
     * @since 0.9
     * @param Configuration $configuration The configuration to resolve the batch update task and correct provider.
     */
    public function configure(Configuration $configuration);

    /**
     * Execute the batch update task for the given interval.
     * The batch update task and the provider can be configured by the method "configure".
     *
     * @since 0.9
     * @param Batch_Update_Task $batch_update_task Stores the current provider and products used for the next update.
     * @param string $update_interval The current update interval from the cron job.
     */
    public function execute(Batch_Update_Task $batch_update_task, $update_interval);
}
