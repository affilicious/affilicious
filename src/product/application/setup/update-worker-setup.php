<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Product\Application\Update\Manager\Update_Manager_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Worker_Setup
{
    /**
     * @var Update_Manager_Interface
     */
    private $update_manager;

    /**
     * @param Update_Manager_Interface $update_manager
     * @since 0.7
     */
    public function __construct(Update_Manager_Interface $update_manager)
    {
        $this->update_manager = $update_manager;
    }

    /**
     * Init the update workers for regularly updated products.
     *
     * @since 0.7
     */
    public function init()
    {
        do_action('affilicious_product_update_worker_setup_before_init');

        $update_workers = apply_filters('affilicious_product_update_worker_setup_init', array());
        $this->update_manager->set_workers($update_workers);

        do_action('affilicious_product_update_worker_setup_after_init', $update_workers);
    }
}
