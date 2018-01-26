<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Update\Update_Manager;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
class Update_Worker_Setup
{
    /**
     * @since 0.9
     * @var Update_Manager
     */
    protected $update_manager;

    /**
     * @since 0.9
     * @param Update_Manager $update_manager
     */
    public function __construct(Update_Manager $update_manager)
    {
        $this->update_manager = $update_manager;
    }

    /**
     * Add all update workers to the update manager.
     *
     * @hook aff_init
     * @since 0.7
     */
    public function init()
    {
        do_action('aff_product_update_worker_before_init');

        $update_workers = apply_filters('aff_product_update_worker_init', []);
        foreach ($update_workers as $update_worker) {
            $this->update_manager->add_worker($update_worker);
        }

        do_action('aff_product_update_worker_after_init', $update_workers);
    }
}
