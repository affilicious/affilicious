<?php
namespace Affilicious\Provider\Factory\In_Memory;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Provider\Factory\Provider_Factory_Interface;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Provider_Factory implements Provider_Factory_Interface
{
    /**
     * The slug generator is responsible to auto-generating slugs.
     *
     * @var Slug_Generator_Interface
     */
    protected $slug_generator;

    /**
     * @since 0.8
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(Slug_Generator_Interface $slug_generator)
    {
        $this->slug_generator = $slug_generator;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create(Name $title, Slug $name, Credentials $credentials)
    {
        do_action('affilicious_provider_factory_before_create');

        $provider = new Provider($title, $name, $credentials);

        do_action('affilicious_provider_factory_after_create', $provider);

        return $provider;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create_from_name(Name $name, Credentials $credentials)
    {
        $provider = $this->create(
            $name,
            $this->slug_generator->generate_from_name($name),
            $credentials
        );

        return $provider;
    }
}
