<?php
namespace Affilicious\Shop\Application\Setup;

use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Provider_Setup
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function init()
    {
        do_action('affilicious_shop_provider_setup_before_init');

        $providers = apply_filters('affilicious_shop_provider_setup_init', array());
        $this->provider_repository->store_all($providers);

        do_action('affilicious_shop_provider_setup_after_init', $providers);
    }
}
