<?php
namespace Affilicious\Detail\Repository;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
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
     * @return Detail_Template_Id|\WP_Error
     */
    public function store(Detail_Template $detail_template);

    /**
     * Delete the detail template by the given ID.
     * The ID of the detail template is going to be null afterwards.
     *
     * @since 0.8
     * @param Detail_Template_Id $detail_template_id
     * @return Detail_Template|\WP_Error
     */
    public function delete(Detail_Template_Id $detail_template_id);

    /**
     * Find an detail template by the ID.
     *
     * @since 0.8
     * @param Detail_Template_Id $detail_template_id
     * @return null|Detail_Template
     */
    public function find_one_by_id(Detail_Template_Id $detail_template_id);

    /**
     * Find one detail template by the name.
     *
     * @since 0.8
     * @param Name $name
     * @return null|Detail_Template
     */
    public function find_one_by_name(Name $name);

    /**
     * Find one detail template by the ID.
     *
     * @since 0.8
     * @param Slug $slug
     * @return null|Detail_Template
     */
    public function find_one_by_slug(Slug $slug);

    /**
     * Find all detail templates.
     *
     * @since 0.8
     * @param array $args
     * @return Detail_Template[]
     */
    public function find_all($args = array());
}
