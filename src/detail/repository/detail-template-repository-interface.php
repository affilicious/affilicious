<?php
namespace Affilicious\Detail\Repository;

use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Detail_Template_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Detail_Template_Repository_Interface
{
    /**
     * Store the given detail template.
     * The ID and the slug of the detail template might be different afterwards.
     *
     * @since 0.8
     * @param Detail_Template $detail_template
     */
    public function store(Detail_Template $detail_template);

    /**
     * Store all detail templates.
     * The ID and the slug of the detail templates might be different afterwards.
     *
     * @since 0.8
     * @param Detail_Template[] $detail_templates
     */
    public function store_all($detail_templates);

    /**
     * Delete the detail template by the given ID.
     * The ID of the detail template is going to be null afterwards.
     *
     * @since 0.8
     * @param Detail_Template_Id $detail_template_id
     */
    public function delete(Detail_Template_Id $detail_template_id);

    /**
     * Delete the detail templates by the given IDs.
     * The ID of the detail templates are going to be null afterwards.
     *
     * @since 0.8
     * @param Detail_Template_Id[] $detail_template_ids
     */
    public function delete_all($detail_template_ids);

    /**
     * Find an detail template by the ID.
     *
     * @since 0.8
     * @param Detail_Template_Id $detail_template_id
     * @return null|Detail_Template
     */
    public function find_by_id(Detail_Template_Id $detail_template_id);

    /**
     * Find all detail templates by the IDs.
     *
     * @since 0.8
     * @param Detail_Template_Id[]$detail_template_ids
     * @return Detail_Template[]
     */
    public function find_all_by_id($detail_template_ids);

    /**
     * Find all detail templates.
     *
     * @since 0.8
     * @return Detail_Template[]
     */
    public function find_all();
}
