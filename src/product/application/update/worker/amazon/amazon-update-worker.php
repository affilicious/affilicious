<?php
namespace Affilicious\Product\Application\Update\Worker\Amazon;

use Affilicious\Product\Application\Update\Worker\Abstract_Update_Worker;
use Affilicious\Product\Application\Update\Worker\Configuration_Resolver_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker extends Abstract_Update_Worker
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function configure(Configuration_Resolver_Interface $configuration)
    {
        $configuration
            ->set('provider', 'amazon')
            ->set('update_interval', 'hourly')
            ->set('force_update_interval', 'twicedaily')
            ->set('min_tasks', 3)
            ->set('max_tasks', 10)
        ;
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
