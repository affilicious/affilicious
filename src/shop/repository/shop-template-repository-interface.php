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
     * @param Shop_Template $shop_template
     */
    public function store(Shop_Template $shop_template);

    /**
     * Store all shop templates.
     * The ID and the slug of the shop templates might be different afterwards.
     *
     * @since 0.8
     * @param Shop_Template[] $shop_templates
     */
    public function store_all($shop_templates);

    /**
     * Delete the shop template by the given ID.
     * The ID of the shop template is going to be null afterwards.
     *
     * @since 0.8
     * @param Shop_Template_Id $shop_template_id
     */
    public function delete(Shop_Template_Id $shop_template_id);

    /**
     * Delete the shop templates by the given IDs.
     * The ID of the shop templates are going to be null afterwards.
     *
     * @since 0.8
     * @param Shop_Template_Id[] $shop_template_ids
     */
    public function delete_all($shop_template_ids);

    /**
     * Find an shop template by the ID.
     *
     * @since 0.8
     * @param Shop_Template_Id $shop_template_id
     * @return null|Shop_Template
     */
    public function find_one_by_id(Shop_Template_Id $shop_template_id);

    /**
     * Find one shop template by the name.
     *
     * @since 0.8
     * @param Name $name
     * @return null|Shop_Template
     */
    public function find_one_by_name(Name $name);

    /**
     * Find one shop template by the ID.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Shop_Template
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find all shop templates by the IDs.
     *
     * @since 0.8
     * @param Shop_Template_Id[]$shop_template_ids
     * @return Shop_Template[]
     */
    public function find_all_by_id($shop_template_ids);

    /**
     * Find all shop templates.
     *
     * @since 0.8
     * @return Shop_Template[]
     */
    public function find_all();
}
