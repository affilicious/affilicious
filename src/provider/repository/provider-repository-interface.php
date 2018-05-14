<?php
namespace Affilicious\Provider\Repository;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Provider\Model\Type;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
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
     * @return bool|\WP_Error
     */
    public function delete(Provider_Id $provider_id);

    /**
     * Delete all providers.
     *
     * @since 0.9.16
     * @return bool|\WP_Error Always true on success or an error on failure.
     */
    public function delete_all();

    /**
     * Find a provider by the ID.
     *
     * @since 0.9.16
     * @param Provider_Id $provider_id
     * @return null|Provider
     */
    public function find(Provider_Id $provider_id);

    /**
     * Find a provider by the slug.
     *
     * @since 0.9.16
     * @param Slug $slug
     * @return null|Provider
     */
    public function find_by_slug(Slug $slug);

    /**
     * Find all providers.
     *
     * @since 0.8
     * @return Provider[]
     */
    public function find_all();

	/**
	 * Find all providers by the type.
	 *
	 * @since 0.9.7
	 * @param Type $type
	 * @return Provider[]
	 */
    public function find_all_by_type(Type $type);

    /**
     * Find a provider by the ID.
     *
     * @deprecated 1.3 Use 'find' instead.
     * @since 0.8
     * @param Provider_Id $provider_id
     * @return null|Provider
     */
    public function find_one_by_id(Provider_Id $provider_id);

    /**
     * Find a provider by the slug.
     *
     * @deprecated 1.3 Use 'find_by_slug' instead.
     * @since 0.8
     * @param Slug $slug
     * @return null|Provider
     */
    public function find_one_by_slug(Slug $slug);
}
