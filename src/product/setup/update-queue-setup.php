<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Update\Task\Broker\Queue\Update_Task_Queue;
use Affilicious\Product\Update\Task\Broker\Update_Task_Broker;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
class Update_Queue_Setup
{
	/**
	 * @since 0.9.20
	 * @var Update_Task_Broker
	 */
	protected $update_task_broker;

	/**
	 * @since 0.9.20
	 * @param Update_Task_Broker $update_task_broker
	 */
	public function __construct(Update_Task_Broker $update_task_broker)
	{
		$this->update_task_broker = $update_task_broker;
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
            $slug = $provider->get_slug();
            $type = $provider->get_type() !== null ? $provider->get_type() : null;
            $queue = new Update_Task_Queue($slug, $type);
            $queues[$slug->get_value()] = $queue;
        }

        $queues = apply_filters('aff_product_update_queue_init', $queues);
        foreach ($queues as $queue) {
	        $this->update_task_broker->add_queue($queue);
        }

        do_action('aff_product_update_queue_after_init', $queues);
    }
}
