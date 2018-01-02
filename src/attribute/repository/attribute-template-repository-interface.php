<?php
namespace Affilicious\Attribute\Repository;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Attribute_Template_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Attribute_Template_Repository_Interface
{
    /**
     * Store the given attribute template.
     * The ID and the slug of the attribute template might be different afterwards.
     *
     * @since 0.8
     * @param Attribute_Template $attribute_template
     * @return Attribute_Template_Id|\WP_Error
     */
    public function store(Attribute_Template $attribute_template);

    /**
     * Delete the attribute template by the given ID.
     * The ID of the attribute template is going to be null afterwards.
     *
     * @since 0.8
     * @param Attribute_Template_Id $attribute_template_id The attribute template ID of the attribute template which will be deleted.
     * @return bool|\WP_Error Always true on success or an error on failure.
     */
    public function delete(Attribute_Template_Id $attribute_template_id);

    /**
     * Delete all attribute templates by the args.
     *
     * @since 0.9.16
     * @param array $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
     * @return bool|\WP_Error Always true on success or an error on failure.
     */
    public function delete_all($args = []);

    /**
     * Find an attribute template by the ID.
     *
     * @since 0.9.16
     * @param Attribute_Template_Id $attribute_template_id
     * @return null|Attribute_Template
     */
    public function find(Attribute_Template_Id $attribute_template_id);

    /**
     * Find all attribute templates.
     *
     * @since 0.8
     * @param array $args Optional. Array or string of arguments. See WP_Term_Query::__construct() for information on accepted arguments. Default empty.
     * @return Attribute_Template[] The found attribute templates.
     */
    public function find_all($args = []);

    /**
     * Find an attribute template by the ID.
     *
     * @deprecated 1.3 Use 'find' instead.
     * @since 0.8
     * @param Attribute_Template_Id $attribute_template_id
     * @return null|Attribute_Template
     */
    public function find_one_by_id(Attribute_Template_Id $attribute_template_id);

    /**
     * Find one attribute template by the name.
     *
     * @deprecated 1.3 Don't use anymore.
     * @since 0.8
     * @param Name $name
     * @return null|Attribute_Template
     */
    public function find_one_by_name(Name $name);

    /**
     * Find one attribute template by the ID.
     *
     * @deprecated 1.3 Use 'find_by_slug' instead.
     * @since 0.8
     * @param Slug $slug
     * @return null|Attribute_Template
     */
    public function find_one_by_slug(Slug $slug);
}
