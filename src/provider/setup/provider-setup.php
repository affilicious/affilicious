<?php
namespace Affilicious\Provider\Setup;

use Affilicious\Provider\Repository\Provider_Repository_Interface;

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
        do_action('aff_provider_before_init');

        $providers = apply_filters('aff_provider_init', []);
        foreach ($providers as $provider) {
            $this->provider_repository->store($provider);
        }

        do_action('aff_provider_after_init', $providers);
    }
}
