<?php
namespace Affilicious\Shop\Application\Setup;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Application\Options\Amazon_Options;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider_Factory_Interface;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;
use Affilicious\Shop\Infrastructure\Factory\In_Memory\In_Memory_Amazon_Provider_Factory;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Provider_Setup
{
    /**
     * @var In_Memory_Amazon_Provider_Factory
     */
    private $amazon_provider_factory;

    /**
     * @since 0.7
     * @param Amazon_Provider_Factory_Interface $amazon_provider_factory
     */
    public function __construct(Amazon_Provider_Factory_Interface $amazon_provider_factory)
    {
        $this->amazon_provider_factory = $amazon_provider_factory;
    }

    /**
     * Init the amazon provider.
     *
     * @since 0.7
     * @param Provider_Interface[] $providers
     * @return Provider_Interface[]
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

        $amazon_provider = $this->amazon_provider_factory->create(
            new Title('Amazon'),
            new Name('amazon'),
            new Key('amazon'),
            new Credentials(array(
                'access_key' => $access_key,
                'secret_key' => $secret_key,
                'country' => $country,
                'associate_tag' => $associate_tag
            ))
        );

        $providers['amazon'] = $amazon_provider;

        return $providers;
    }
}
