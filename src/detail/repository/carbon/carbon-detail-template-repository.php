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
use Webmozart\Assert\Assert;

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
        $detail_template->has_id() ? $this->update($detail_template) : $this->insert($detail_template);

        add_action('affilicious_detail_template_repository_store', $detail_template);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store_all($detail_templates)
    {
        Assert::allIsInstanceOf($detail_templates, Detail_Template::class);

        foreach ($detail_templates as $detail_template) {
            $this->store($detail_template);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Detail_Template_Id $detail_template_id)
    {
        wp_delete_term(
            $detail_template_id->get_value(),
            Detail_Template::TAXONOMY
        );

        add_action('affilicious_detail_template_repository_delete', $detail_template_id);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete_all($detail_template_ids)
    {
        Assert::allIsInstanceOf($detail_template_ids, Detail_Template_Id::class);

        foreach ($detail_template_ids as $detail_template_id) {
            $this->delete($detail_template_id);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_by_id(Detail_Template_Id $detail_template_id)
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
    public function find_all_by_id($detail_template_ids)
    {
        Assert::allIsInstanceOf($detail_template_ids, Detail_Template_Id::class);

        $detail_templates = array();

        foreach ($detail_template_ids as $detail_template_id) {
            $detail_template = $this->find_by_id($detail_template_id);
            if($detail_template === null) {
                $detail_templates[] = $detail_template;
            }
        }

        return $detail_templates;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all()
    {
        $terms = get_terms(array(
            'taxonomy' => Detail_Template::TAXONOMY,
            'hide_empty' => false
        ));

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

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to insert the detail template %s (%s).',
                $detail_template->get_name()->get_value(),
                $detail_template->get_slug()->get_value()
            ));
        }

        $detail_template->set_id(new Detail_Template_Id($term['term_id']));

        add_term_meta(
            $detail_template->get_id()->get_value(),
            self::TYPE,
            $detail_template->get_type()->get_value()
        );

        if(!empty($detail_template->get_unit())) {
            add_term_meta(
                $detail_template->get_id()->get_value(),
                self::UNIT,
                $detail_template->get_unit()->get_value()
            );
        }
    }

    /**
     * Update an existing detail template in the database.
     *
     * @since 0.8
     * @param Detail_Template $detail_template
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

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to update the detail template %s (%s).',
                $detail_template->get_name()->get_value(),
                $detail_template->get_slug()->get_value()
            ));
        }

        if(!empty($term['slug'])) {
            $detail_template->set_slug(new Slug($term['slug']));
        }

        update_term_meta(
            $detail_template->get_id()->get_value(),
            self::TYPE,
            $detail_template->get_type()->get_value()
        );

        if(!empty($detail_template->get_unit())) {
            update_term_meta(
                $detail_template->get_id()->get_value(),
                self::UNIT,
                $detail_template->get_unit()->get_value()
            );
        }
    }
}
