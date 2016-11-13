<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Repository_Interface extends Repository_Interface
{
    /**
     * Store the given provider.
     * This method will fail, if the name of the provider is not unique.
     *
     * @since 0.7
     * @param Provider_Interface $provider
     * @return Provider_Interface
     */
    public function store(Provider_Interface $provider);

    /**
     * Store all given providers.
     * This method will fail, if the name of the provider is not unique.
     *
     * @since 0.7
     * @param Provider_Interface[] $providers
     * @return null[]|Provider_Interface[]
     */
    public function store_all($providers);

    /**
     * Delete the provider by the given ID.
     *
     * @since 0.7
     * @param Provider_Id $provider_Id
     * @return null|Provider_Interface
     */
    public function delete(Provider_Id $provider_Id);

    /**
     * Delete all providers by the given ID.
     *
     * @since 0.7
     * @param Provider_Id[] $provider_ids
     * @return null[]|Provider_Interface[]
     */
    public function delete_all($provider_ids);

    /**
     * Find a shop provider by the given ID.
     *
     * @since 0.7
     * @param Provider_Id $provider_Id
     * @return null|Provider_Interface
     */
    public function find_by_id(Provider_Id $provider_Id);

    /**
     * Find a shop provider by the given name.
     *
     * @since 0.7
     * @param Name $name
     * @return null|Provider_Interface
     */
    public function find_by_name(Name $name);

    /**
     * Find all providers.
     *
     * @since 0.7
     * @return Provider_Interface[]
     */
    public function find_all();
}
