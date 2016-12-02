<?php
namespace Affilicious\Product\Application\Update\Worker\Amazon;

use Affilicious\Product\Application\Update\Configuration\Configuration;
use Affilicious\Product\Application\Update\Worker\Abstract_Update_Worker;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker extends Abstract_Update_Worker
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function configure()
    {
        $config = new Configuration(array(
            'provider' => 'amazon',
            'update_interval' => 'hourly',
            'force_update_interval' => 'twicedaily',
            'min_tasks' => 1,
            'max_tasks' => 10,
        ));

        return $config;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function execute($update_tasks)
    {
        // TODO: Implement execute() method.
    }
}
