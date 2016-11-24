<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Queue\Update_Queue;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;
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
     * @since 0.7
     * @param Provider_Interface[] $providers
     */
    public function init($providers)
    {
        foreach ($providers as $provider) {
            $name = $provider->get_name();
            $queue = new Update_Queue($name->get_value());

            $this->mediator->add_queue($queue);
        }
    }
}
