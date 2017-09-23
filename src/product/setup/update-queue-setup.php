<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Update\Queue\Update_Queue;
use Affilicious\Product\Update\Update_Manager;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Queue_Setup
{
    /**
     * @var Update_Manager
     */
    private $update_manager;

    /**
     * @since 0.9
     * @param Update_Manager $update_manager
     */
    public function __construct(Update_Manager $update_manager)
    {
        $this->update_manager = $update_manager;
    }

    /**
     * Add all update queues to the update manager.
     *
     * @hook aff_provider_after_init
     * @since 0.7
     * @param Provider[] $providers The list of all available shop providers.
     */
    public function init($providers)
    {
        do_action('aff_product_update_queue_before_init');

        $queues = [];
        foreach ($providers as $provider) {
            $slug = $provider->get_slug()->get_value();
            $type = $provider->get_type() !== null ? $provider->get_type()->get_value() : null;
            $queue = new Update_Queue($slug, $type);
            $queues[$slug] = $queue;
        }

        $queues = apply_filters('aff_product_update_queue_init', $queues);
        foreach ($queues as $queue) {
            $this->update_manager->add_queue($queue);
        }

        do_action('aff_product_update_queue_after_init', $queues);
    }
}
