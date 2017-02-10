<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Update\Queue\Update_Queue;
use Affilicious\Provider\Model\Provider;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Mediator_Setup
{
    /**
     * @var Update_Mediator_Interface
     */
    private $mediator;

    /**
     * @since 0.7
     * @param Update_Mediator_Interface $mediator
     */
    public function __construct(Update_Mediator_Interface $mediator)
    {
        $this->mediator = $mediator;
    }

    /**
     * Init the update mediator by adding all update workers.
     *
     * @hook affilicious_provider_setup_after_init
     * @since 0.7
     * @param Provider[] $providers
     */
    public function init($providers)
    {
        foreach ($providers as $provider) {
            $slug = $provider->get_slug();
            $queue = new Update_Queue($slug);

            $this->mediator->add_queue($queue);
        }
    }
}
