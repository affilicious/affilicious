<?php
namespace Affilicious\Provider\Setup;

use Affilicious\Provider\Repository\Provider_Repository_Interface;
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
     * @since 0.8
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * @hook init
     * @since 0.8
     */
    public function init()
    {
        do_action('affilicious_provider_setup_before_init');

        $providers = apply_filters('affilicious_provider_setup_init', array());
        $this->provider_repository->store_all($providers);

        do_action('affilicious_provider_setup_after_init', $providers);
    }
}
