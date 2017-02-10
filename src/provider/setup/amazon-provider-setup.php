<?php
namespace Affilicious\Provider\Setup;

use Affilicious\Common\Model\Name;
use Affilicious\Provider\Factory\Amazon_Provider_Factory_Interface;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Options\Amazon_Options;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Provider_Setup
{
    /**
     * @var Amazon_Provider_Factory_Interface
     */
    private $amazon_provider_factory;

    /**
     * @since 0.8
     * @param Amazon_Provider_Factory_Interface $amazon_provider_factory
     */
    public function __construct(Amazon_Provider_Factory_Interface $amazon_provider_factory)
    {
        $this->amazon_provider_factory = $amazon_provider_factory;
    }

    /**
     * Init the Amazon provider.
     *
     * @hook affilicious_provider_setup_init
     * @since 0.8
     * @param Provider[] $providers
     * @return Provider[]
     */
    public function init($providers)
    {
        $access_key = carbon_get_theme_option(Amazon_Options::ACCESS_KEY);
        $secret_key = carbon_get_theme_option(Amazon_Options::SECRET_KEY);
        $country = carbon_get_theme_option(Amazon_Options::COUNTRY);
        $associate_tag = carbon_get_theme_option(Amazon_Options::ASSOCIATE_TAG);

        if(empty($access_key) || empty($secret_key) || empty($country) || empty($associate_tag)) {
            return $providers;
        }

        $amazon_provider = $this->amazon_provider_factory->create_from_name(
            new Name('Amazon'),
            new Credentials(array(
                Amazon_Provider::ACCESS_KEY => $access_key,
                Amazon_Provider::SECRET_KEY => $secret_key,
                Amazon_Provider::COUNTRY => $country,
                Amazon_Provider::ASSOCIATE_TAG => $associate_tag
            ))
        );

        $providers[$amazon_provider->get_slug()->get_value()] = $amazon_provider;

        return $providers;
    }
}
