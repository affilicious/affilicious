<?php
namespace Affilicious\Attribute\Repository;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Attribute_Template_Id;

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
     */
    public function store(Attribute_Template $attribute_template);

    /**
     * Store all attribute templates.
     * The ID and the slug of the attribute templates might be different afterwards.
     *
     * @since 0.8
     * @param Attribute_Template[] $attribute_templates
     */
    public function store_all($attribute_templates);

    /**
     * Delete the attribute template by the given ID.
     * The ID of the attribute template is going to be null afterwards.
     *
     * @since 0.8
     * @param Attribute_Template_Id $attribute_template_id
     */
    public function delete(Attribute_Template_Id $attribute_template_id);

    /**
     * Delete the attribute templates by the given IDs.
     * The ID of the attribute templates are going to be null afterwards.
     *
     * @since 0.8
     * @param Attribute_Template_Id[] $attribute_template_ids
     */
    public function delete_all($attribute_template_ids);

    /**
     * Find an attribute template by the ID.
     *
     * @since 0.8
     * @param Attribute_Template_Id $attribute_template_id
     * @return null|Attribute_Template
     */
    public function find_by_id(Attribute_Template_Id $attribute_template_id);

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
     * @return Attribute_Template[]
     */
    public function find_all();
}
