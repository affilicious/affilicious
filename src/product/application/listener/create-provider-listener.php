<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Application\Update\Queue\Update_Mediator_Interface;
use Affilicious\Product\Application\Update\Queue\Update_Queue;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Create_Provider_Listener
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
     * Listen for the 'affilicious_shop_provider_after_create' action.
     *
     * @since 0.7
     * @param Provider_Interface $provider
     */
    public function listen(Provider_Interface $provider)
    {
        $name = $provider->get_name();
        $queue = new Update_Queue($name->get_value());

        $this->mediator->add_queue($queue);
    }
}
