<?php
namespace Affilicious\Product\Application\Listener;

use Affilicious\Product\Application\Updater\Request\Update_Request_Exchange_Interface;
use Affilicious\Product\Application\Updater\Request\Update_Request_Queue;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Create_Provider_Listener
{
    /**
     * @var Update_Request_Exchange_Interface
     */
    private $exchange;

    /**
     * @since 0.7
     * @param Update_Request_Exchange_Interface $exchange
     */
    public function __construct(Update_Request_Exchange_Interface $exchange)
    {
        $this->exchange = $exchange;
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
        $queue = new Update_Request_Queue($name->get_value());

        $this->exchange->add_queue($queue);
    }
}
