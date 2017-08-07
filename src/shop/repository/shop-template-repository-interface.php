<?php
namespace Affilicious\Shop\Repository;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
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
     * @since 0.8
     * @param Shop_Template_Id $shop_template_id The shop template ID of the shop template which will be deleted.
     * @return Shop_Template|\WP_Error Either the deleted shop template or an error.
     */
    public function delete(Shop_Template_Id $shop_template_id);

    /**
     * Find an shop template by the ID.
     *
     * @since 0.8
     * @param Shop_Template_Id $shop_template_id The shop template ID for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_id(Shop_Template_Id $shop_template_id);

    /**
     * Find one shop template by the name.
     *
     * @since 0.8
     * @param Name $name The name for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_name(Name $name);

    /**
     * Find one shop template by the slug.
     *
     * @since 0.8
     * @param Slug $slug The slug for the search.
     * @return null|Shop_Template Either the shop template or no result.
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find all shop templates.
     *
     * @since 0.8
     * @param array|string $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
     * @return Shop_Template[] The found shop templates.
     */
    public function find_all($args = array());
}
