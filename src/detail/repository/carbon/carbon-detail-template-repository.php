<?php
namespace Affilicious\Detail\Repository\Carbon;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Detail_Template_Id;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Detail_Template_Repository extends Abstract_Carbon_Repository implements Detail_Template_Repository_Interface
{
    const TYPE = '_affilicious_detail_template_type';
    const UNIT = '_affilicious_detail_template_unit';

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store(Detail_Template $detail_template)
    {
        $detail_template_id = $detail_template->has_id() ?
            $this->update($detail_template) :
            $this->insert($detail_template);

        return $detail_template_id;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Detail_Template_Id $detail_template_id)
    {
        $detail_template = $this->find_one_by_id($detail_template_id);
        if($detail_template === null) {
            return new \WP_Error('aff_detail_template_not_found', sprintf(
                'Detail template #%s not found in the database.',
                $detail_template_id->get_value()
            ));
        }

        $result = wp_delete_term(
            $detail_template_id->get_value(),
            Detail_Template::TAXONOMY
        );

        if($result === 0) {
            return new \WP_Error('aff_invalid_deletion_of_default_category', sprintf(
                "It's not allowed to delete a default Wordpress category",
                $detail_template_id->get_value()
            ));
        }

        if($result instanceof \WP_Error) {
            return $result;
        }

        $detail_template->set_id(null);

        return $detail_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_one_by_id(Detail_Template_Id $detail_template_id)
    {
        $term = get_term($detail_template_id->get_value(), Detail_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $detail_template = $this->build($term);

        return $detail_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_one_by_name(Name $name)
    {
        $term = get_term_by('name', $name->get_value(), Detail_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $detail_template = $this->build($term);

        return $detail_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_one_by_slug(Slug $slug)
    {
        $term = get_term_by('slug', $slug->get_value(), Detail_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $detail_template = $this->build($term);

        return $detail_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all($args = array())
    {
        $args['taxonomy'] = Detail_Template::TAXONOMY;
        $args = wp_parse_args($args, array(
            'hide_empty' => false
        ));

        $terms = get_terms($args);

        if(empty($terms) || $terms instanceof \WP_Error) {
            return array();
        }

        $detail_templates = array();
        foreach ($terms as $term) {
            $detail_template = $this->build($term);
            if($detail_template !== null) {
                $detail_templates[] = $detail_template;
            }
        }

        return $detail_templates;
    }

    /**
     * Build the detail template from the term.
     *
     * @since 0.8
     * @param \WP_Term $term
     * @return Detail_Template
     */
    protected function build(\WP_Term $term)
    {
        $id = new Detail_Template_Id($term->term_id);
        $name = new Name($term->name);
        $slug = new Slug($term->slug);
        $type = null;
        $unit = null;

        if($raw_type = carbon_get_term_meta($id->get_value(), self::TYPE)) {
            $type = new Type($raw_type);
        }

        if($raw_unit = carbon_get_term_meta($id->get_value(), self::UNIT)) {
            $unit = new Unit($raw_unit);
        }

        $detail_template = new Detail_Template($name, $slug, $type, $unit);
        $detail_template->set_id($id);

        return $detail_template;
    }

    /**
     * Insert a new detail template into the database.
     *
     * @since 0.8
     * @param Detail_Template $detail_template
     * @return Detail_Template_Id|\WP_Error
     */
    protected function insert(Detail_Template $detail_template)
    {
        $term = wp_insert_term(
            $detail_template->get_name()->get_value(),
            Detail_Template::TAXONOMY,
            array(
                'slug' => $detail_template->get_slug()->get_value(),
            )
        );

        if(empty($term)) {
            return new \WP_Error('aff_detail_template_not_stored', sprintf(
                'Failed to store the detail template #%s (%s) into the database.',
                $detail_template->get_id()->get_value(),
                $detail_template->get_name()->get_value()
            ));
        }

        if($term instanceof \WP_Error) {
            return $term;
        }

        $detail_template->set_id(new Detail_Template_Id($term['term_id']));

        $result = add_term_meta(
            $detail_template->get_id()->get_value(),
            self::TYPE,
            $detail_template->get_type()->get_value()
        );

        if($result instanceof \WP_Error) {
            return $result;
        }

        if($detail_template->has_unit()) {
            add_term_meta(
                $detail_template->get_id()->get_value(),
                self::UNIT,
                $detail_template->get_unit()->get_value()
            );
        }

        return $detail_template->get_id();
    }

    /**
     * Update an existing detail template in the database.
     *
     * @since 0.8
     * @param Detail_Template $detail_template
     * @return Detail_Template_Id|\WP_Error
     */
    protected function update(Detail_Template $detail_template)
    {
        $term = wp_update_term(
            $detail_template->get_id()->get_value(),
            Detail_Template::TAXONOMY,
            array(
                'name' => $detail_template->get_name()->get_value(),
                'slug' => $detail_template->get_slug()->get_value(),
            )
        );

        if(empty($term)) {
            return new \WP_Error('aff_detail_template_not_stored', sprintf(
                'Failed to store the detail template #%s (%s) into the database.',
                $detail_template->get_id()->get_value(),
                $detail_template->get_name()->get_value()
            ));
        }

        if($term instanceof \WP_Error) {
            return $term;
        }

        if(!empty($term['slug'])) {
            $detail_template->set_slug(new Slug($term['slug']));
        }

        $result = update_term_meta(
            $detail_template->get_id()->get_value(),
            self::TYPE,
            $detail_template->get_type()->get_value()
        );

        if($result instanceof \WP_Error) {
            return $result;
        }

        if($detail_template->has_unit()) {
            update_term_meta(
                $detail_template->get_id()->get_value(),
                self::UNIT,
                $detail_template->get_unit()->get_value()
            );
        }

        return $detail_template->get_id();
    }
}
