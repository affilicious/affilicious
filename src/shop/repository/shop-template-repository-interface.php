<?php
namespace Affilicious\Shop\Repository;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Model\Shop_Template_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Template_Repository_Interface
{
    /**
     * Store the given shop template.
     * The ID and the slug of the shop template might be different afterwards.
     *
     * @since 0.8
     * @param Shop_Template $shop_template The shop template which will be stored.
     * @return Shop_Template_Id|\WP_Error Either the stored shop template ID or an error.
     */
    public function store(Shop_Template $shop_template);

    /**
     * Delete the shop template by the given ID.
     * The ID of the shop template is going to be null afterwards.
     *
     * @since 0.9.16
     * @param Shop_Template_Id $shop_template_id The shop template ID of the shop template which will be deleted.
     * @return true|\WP_Error Always true on success or an error on failure.
     */
    public function delete(Shop_Template_Id $shop_template_id);

    /**
     * Delete all shop templates by the args.
     *
     * @since 0.9.16
     * @param array $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
     * @return bool|\WP_Error Always returns true on success and an error on failure.
     */
    public function delete_all($args = []);

    /**
     * Find an shop template by the ID.
     *
     * @since 0.9.16
     * @param Shop_Template_Id $shop_template_id The shop template ID for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find(Shop_Template_Id $shop_template_id);

    /**
     * Find one shop template by the slug.
     *
     * @since 0.9.16
     * @param Slug $slug The slug for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_by_slug(Slug $slug);

    /**
     * Find all shop templates.
     *
     * @since 0.8
     * @param array|string $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
     * @return Shop_Template[] The found shop templates.
     */
    public function find_all($args = []);

	/**
	 * Find all shop templates by the provider ID.
	 *
     * @deprecated 1.3 Don't use anymore.
	 * @since 0.9.4
	 * @param Provider_Id $provider_id The ID of the provider.
	 * @return Shop_Template[] The found shop templates.
	 */
	public function find_all_by_provider_id(Provider_Id $provider_id);

    /**
     * Find an shop template by the ID.
     *
     * @deprecated 1.3 Use 'find' instead.
     * @since 0.8
     * @param Shop_Template_Id $shop_template_id The shop template ID for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_id(Shop_Template_Id $shop_template_id);

    /**
     * Find one shop template by the slug.
     *
     * @deprecated 1.3 Use 'find_by_slug' instead.
     * @since 0.8
     * @param Slug $slug The slug for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find one shop template by the name.
     *
     * @deprecated 1.3 Don't use anymore.
     * @since 0.8
     * @param Name $name The name for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_name(Name $name);
}
