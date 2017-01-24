<?php
namespace Affilicious\Provider\Repository\Carbon;

use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Provider_Repository implements Provider_Repository_Interface
{
    const CURRENT_ID = '_affilicious_provider_current_id';
    const ID_TEMPLATE = '_affilicious_provider_%s_id';

    /**
     * @var int
     */
    private $current_id;

    /**
     * @var Provider[]
     */
    private $providers;

    /**
     * @var Key_Generator_Interface
     */
    private $key_generator;

    /**
     * @since 0.8
     * @param Key_Generator_Interface $key_generator
     */
    public function __construct(Key_Generator_Interface $key_generator)
    {
        $this->key_generator = $key_generator;

        if(($this->current_id = (int) carbon_get_theme_option(self::CURRENT_ID)) == false) {
            $this->current_id = 1;

            update_option(self::CURRENT_ID, $this->current_id);
        }

        $this->providers = array();
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store(Provider $provider)
    {
        $this->prepare_provider_id($provider);

        $this->providers[$provider->get_slug()->get_value()] = $provider;

        return $provider;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store_all($providers)
    {
        Assert::allIsInstanceOf($providers, Provider::class);

        foreach ($providers as $provider) {
            $this->store($provider);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Provider_Id $provider_id)
    {
        $deletedProvider = $this->find_by_id($provider_id);
        if($deletedProvider === null) {
            return null;
        }

        unset($this->providers[$deletedProvider->get_name()->get_value()]);

        return $deletedProvider;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete_all($provider_ids)
    {
        Assert::allIsInstanceOf($provider_ids, Provider_Id::class);

        foreach ($provider_ids as $provider_id) {
            $this->delete($provider_id);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_by_id(Provider_Id $provider_id)
    {
        foreach ($this->providers as $provider) {
            if($provider->get_id()->is_equal_to($provider_id)) {
                return $this->prepare_provider_id($provider);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all_by_id($provider_ids)
    {
        Assert::allIsInstanceOf($provider_ids, Provider_Id::class);

        $providers = array();
        foreach ($this->providers as $provider) {
            foreach ($provider_ids as $provider_id) {
                if($provider->get_id()->is_equal_to($provider_id)) {
                    $providers[] = $this->prepare_provider_id($provider);
                }
            }
        }

        return $providers;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_by_slug(Slug $name)
    {
        return isset($this->providers[$name->get_value()]) ? $this->prepare_provider_id($this->providers[$name->get_value()]) : null;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all()
    {
        $providers = array();
        foreach ($this->providers as $provider) {
            $providers[] = $this->prepare_provider_id($provider);
        }

        return $providers;
    }

    /**
     * @since 0.8
     * @param Slug $slug
     * @return Provider_Id|null
     */
    private function find_provider_id(Slug $slug)
    {
        $key = $this->key_generator->generate_from_slug($slug);
        $option = sprintf(self::ID_TEMPLATE, $key->get_value());
        $id = carbon_get_theme_option($option);

        return !empty($id) ? new Provider_Id($id) : null;
    }

    /**
     * Store the provider ID for the slug.
     *
     * @since 0.8
     * @param Provider_Id $id
     * @param Slug $slug
     */
    private function store_provider_id(Provider_Id $id, Slug $slug)
    {
        $key = $this->key_generator->generate_from_slug($slug);
        $option = sprintf(self::ID_TEMPLATE, $key->get_value());
        update_option($option, $id->get_value());
    }

    /**
     * Get the next free provider ID.
     *
     * @since 0.8
     * @return Provider_Id
     */
    private function get_next_provider_id()
    {
        $id = new Provider_Id($this->current_id);

        $this->current_id++;
        update_option(self::CURRENT_ID, $this->current_id);

        return $id;
    }

    /**
     * Prepare the provider ID.
     *
     * @since 0.8
     * @param Provider $provider
     * @return Provider
     */
    private function prepare_provider_id(Provider $provider)
    {
        $id = $this->find_provider_id($provider->get_slug());
        if($id === null) {
            $id = $this->get_next_provider_id();
            $this->store_provider_id($id, $provider->get_slug());
        }

        $provider->set_id($id);

        return $provider;
    }
}
