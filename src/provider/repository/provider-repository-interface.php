<?php
namespace Affilicious\Provider\Repository;

use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Repository_Interface
{
    /**
     * Store the given provider.
     *
     * @since 0.8
     * @param Provider $provider
     */
    public function store(Provider $provider);

    /**
     * Store all given providers.
     *
     * @since 0.8
     * @param Provider[] $providers
     */
    public function store_all($providers);

    /**
     * Delete the provider by the ID.
     * The ID of the provider is going to be null afterwards.
     *
     * @since 0.8
     * @param Provider_Id $provider_id
     */
    public function delete(Provider_Id $provider_id);

    /**
     * Delete all providers by the IDs.
     * The IDs of the providers are going to be null afterwards.
     *
     * @since 0.8
     * @param Provider_Id[] $provider_ids
     */
    public function delete_all($provider_ids);

    /**
     * Find a provider by the ID.
     *
     * @since 0.8
     * @param Provider_Id $provider_id
     * @return null|Provider
     */
    public function find_one_by_id(Provider_Id $provider_id);

    /**
     * Find all providers by the IDs.
     *
     * @since 0.8
     * @param Provider_Id[] $provider_ids
     * @return Provider[]
     */
    public function find_all_by_id($provider_ids);

    /**
     * Find a provider by the slug.
     *
     * @since 0.8
     * @param Slug $name
     * @return null|Provider
     */
    public function find_one_by_slug(Slug $name);

    /**
     * Find all providers.
     *
     * @since 0.8
     * @return Provider[]
     */
    public function find_all();
}
