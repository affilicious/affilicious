<?php
namespace Affilicious\Shop\Infrastructure\Repository\In_Memory;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Shop\Domain\Exception\Unique_Name_Exception;
use Affilicious\Shop\Domain\Model\Provider\Provider_Id;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Provider_Repository implements Provider_Repository_Interface
{
    /**
     * @var int
     */
    protected $current_id;

    /**
     * @var Provider_Interface[]
     */
    protected $providers;

    /**
     * @since 0.7
     */
    public function __construct()
    {
        $this->current_id = 0;
        $this->providers = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function store(Provider_Interface $provider)
    {
        if(isset($this->providers[$provider->get_name()->get_value()])) {
            throw new Unique_Name_Exception(sprintf(
                'The provider with the name "%s" is already stored in the repository. Please use another name.',
                $provider->get_name()->get_value()
            ));
        }

        if(!$provider->has_id()) {
            $provider->set_id(new Provider_Id($this->current_id));
            $this->current_id++;
        }

        $this->providers[$provider->get_name()->get_value()] = $provider;

        return $provider;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function store_all($providers)
    {
        $stored_providers = array();
        foreach ($providers as $provider) {
            $stored_providers[] = $this->store($provider);
        }

        return $stored_providers;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function delete(Provider_Id $provider_Id)
    {
        $deletedProvider = $this->find_by_id($provider_Id);
        if($deletedProvider === null) {
            return null;
        }

        unset($this->providers[$deletedProvider->get_name()->get_value()]);

        return $deletedProvider;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function delete_all($provider_ids)
    {
        $deleted_providers = array();
        foreach ($provider_ids as $provider_id) {
            $deleted_providers[] = $this->delete($provider_id);
        }

        return $deleted_providers;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function find_by_id(Provider_Id $provider_Id)
    {
        foreach ($this->providers as $provider) {
            if($provider->get_id()->is_equal_to($provider_Id)) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function find_by_name(Name $name)
    {
        return isset($this->providers[$name->get_value()]) ? $this->providers[$name->get_value()] : null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function find_all()
    {
        $providers = array_values($this->providers);

        return $providers;
    }
}
