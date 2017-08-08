<?php
namespace Affilicious\Provider\Repository;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Product_Id;
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
     * @return Product_Id|\WP_Error
     */
    public function store(Provider $provider);

    /**
     * Delete the provider by the ID.
     * The ID of the provider is going to be null afterwards.
     *
     * @since 0.8
     * @param Provider_Id $provider_id
     * @return Provider|\WP_Error
     */
    public function delete(Provider_Id $provider_id);

    /**
     * Find a provider by the ID.
     *
     * @since 0.8
     * @param Provider_Id $provider_id
     * @return null|Provider
     */
    public function find_one_by_id(Provider_Id $provider_id);

    /**
     * Find a provider by the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Provider
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find all providers.
     *
     * @since 0.8
     * @return Provider[]
     */
    public function find_all();
}
