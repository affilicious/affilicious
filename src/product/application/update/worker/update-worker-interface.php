<?php
namespace Affilicious\Product\Application\Update\Worker;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Product\Application\Update\Configuration\Configuration_Interface;
use Affilicious\Product\Application\Update\Task\Batch_Update_Task_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Update_Worker_Interface
{
    /**
     * @since 0.7
     * @param Name $name
     */
    public function __construct(Name $name);

    /**
     * Get the unique name of the worker.
     *
     * @since 0.7
     * @return Name
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
