<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Application\Update\Manager\Update_Manager_Interface;
use Affilicious\Product\Application\Update\Worker\Update_Worker_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Create_Worker_Listener
{
    /**
     * @var Update_Manager_Interface
     */
    private $manager;

    /**
     * @since 0.7
     * @param Update_Manager_Interface $manager
     */
    public function __construct(Update_Manager_Interface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Listen for the 'affilicious_product_update_worker_create' action.
     *
     * @since 0.7
     * @param Update_Worker_Interface $worker
     */
    public function listen(Update_Worker_Interface $worker)
    {
        $this->manager->add_worker($worker);
    }
}
