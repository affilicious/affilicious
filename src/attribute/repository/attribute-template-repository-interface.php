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
     * @param Attribute_Template_Id $attribute_template_id
     * @return Attribute_Template|\WP_Error
     */
    public function delete(Attribute_Template_Id $attribute_template_id);

    /**
     * Find an attribute template by the ID.
     *
     * @since 0.8
     * @param Attribute_Template_Id $attribute_template_id
     * @return null|Attribute_Template
     */
    public function find_one_by_id(Attribute_Template_Id $attribute_template_id);

    /**
     * Find one attribute template by the name.
     *
     * @since 0.8
     * @param Name $name
     * @return null|Attribute_Template
     */
    public function find_one_by_name(Name $name);

    /**
     * Find one attribute template by the ID.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Attribute_Template
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find all attribute templates by the IDs.
     *
     * @since 0.8
     * @param Attribute_Template_Id[]$attribute_template_ids
     * @return Attribute_Template[]
     */
    public function find_all_by_id($attribute_template_ids);

    /**
     * Find all attribute templates.
     *
     * @since 0.8
     * @param array $args
     * @return Attribute_Template[]
     */
    public function find_all($args = array());
}
