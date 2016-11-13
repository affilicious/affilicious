<?php
namespace Affilicious\Shop\Application\Setup;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;
use Affilicious\Shop\Infrastructure\Factory\In_Memory\In_Memory_Amazon_Provider_Factory;
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
     * @var In_Memory_Amazon_Provider_Factory
     */
    private $amazon_provider_factory;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     * @param In_Memory_Amazon_Provider_Factory $amazon_provider_factory
     */
    public function __construct(
        Provider_Repository_Interface $provider_repository,
        In_Memory_Amazon_Provider_Factory $amazon_provider_factory
    )
    {
        $this->provider_repository = $provider_repository;
        $this->amazon_provider_factory = $amazon_provider_factory;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function init()
    {
        do_action('affilicious_shop_provider_setup_before_init');

        $providers = array();

        do_action('affilicious_shop_provider_before_init');
        $providers = apply_filters('affilicious_shop_provider_init', $providers);
        do_action('affilicious_shop_provider_after_init', $providers);

        $this->provider_repository->store_all($providers);

        do_action('affilicious_shop_provider_setup_after_init');
    }

    /**
     * Add the amazon provider to all providers.
     *
     * @since 0.7
     * @param Provider_Interface[] $providers
     * @return Provider_Interface[]
     */
    public function init_amazon($providers)
    {
        $access_key_id = carbon_get_theme_option('affilicious_options_amazon_container_credentials_tab_access_key_id_field');
        $secret_access_key = carbon_get_theme_option('affilicious_options_amazon_container_credentials_tab_secret_access_key_field');
        $country = carbon_get_theme_option('affilicious_options_amazon_container_credentials_tab_country_field');
        $partner_tag = carbon_get_theme_option('affilicious_options_amazon_container_credentials_tab_partner_tag_field');

        if(empty($access_key_id) || empty($secret_access_key) || empty($country) || empty($partner_tag)) {
            return $providers;
        }

        $amazon_provider = $this->amazon_provider_factory->create(
            new Title('Amazon'),
            new Name('amazon'),
            new Key('amazon'),
            new Credentials(array(
                'access_key_id' => $access_key_id,
                'secret_access_key' => $secret_access_key,
                'country' => $country,
                'partner_tag' => $partner_tag
            ))
        );

        $providers['amazon'] = $amazon_provider;

        return $providers;
    }
}
