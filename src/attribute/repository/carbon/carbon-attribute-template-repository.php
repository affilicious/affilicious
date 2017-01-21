<?php
namespace Affilicious\Attribute\Repository\Carbon;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Attribute_Template_Id;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Unit;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Repository\Carbon\Abstract_Carbon_Repository;
use Webmozart\Assert\Assert;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Attribute_Template_Repository extends Abstract_Carbon_Repository implements Attribute_Template_Repository_Interface
{
    const TYPE = '_affilicious_attribute_template_type';
    const UNIT = '_affilicious_attribute_template_unit';

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store(Attribute_Template $attribute_template)
    {
        $attribute_template->has_id() ? $this->update($attribute_template) : $this->insert($attribute_template);

        add_action('affilicious_attribute_template_repository_store', $attribute_template);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store_all($attribute_templates)
    {
        Assert::allIsInstanceOf($attribute_templates, Attribute_Template::class);

        foreach ($attribute_templates as $attribute_template) {
            $this->store($attribute_template);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Attribute_Template_Id $attribute_template_id)
    {
        wp_delete_term(
            $attribute_template_id->get_value(),
            Attribute_Template::TAXONOMY
        );

        add_action('affilicious_attribute_template_repository_delete', $attribute_template_id);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete_all($attribute_template_ids)
    {
        Assert::allIsInstanceOf($attribute_template_ids, Attribute_Template_Id::class);

        foreach ($attribute_template_ids as $attribute_template_id) {
            $this->delete($attribute_template_id);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_by_id(Attribute_Template_Id $attribute_template_id)
    {
        $term = get_term($attribute_template_id->get_value(), Attribute_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $attribute_template = $this->build($term);

        return $attribute_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all_by_id($attribute_template_ids)
    {
        Assert::allIsInstanceOf($attribute_template_ids, Attribute_Template_Id::class);

        $attribute_templates = array();

        foreach ($attribute_template_ids as $attribute_template_id) {
            $attribute_template = $this->find_by_id($attribute_template_id);
            if($attribute_template === null) {
                $attribute_templates[] = $attribute_template;
            }
        }

        return $attribute_templates;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all()
    {
        $terms = get_terms(array(
            'taxonomy' => Attribute_Template::TAXONOMY,
            'hide_empty' => false
        ));

        if(empty($terms) || $terms instanceof \WP_Error) {
            return array();
        }

        $attribute_templates = array();
        foreach ($terms as $term) {
            $attribute_template = $this->build($term);
            if($attribute_template !== null) {
                $attribute_templates[] = $attribute_template;
            }
        }

        return $attribute_templates;
    }

    /**
     * Build the attribute template from the term.
     *
     * @since 0.8
     * @param \WP_Term $term
     * @return Attribute_Template
     */
    protected function build(\WP_Term $term)
    {
        $id = new Attribute_Template_Id($term->term_id);
        $name = new Name($term->name);
        $slug = new Slug($term->slug);
        $type = null;
        $unit = null;

        if($raw_type = carbon_get_term_meta($id->get_value(), self::TYPE)) {
            $type = new Type($raw_type);
        } else {
            return null;
        }

        if($raw_unit = carbon_get_term_meta($id->get_value(), self::UNIT)) {
            $unit = new Unit($raw_unit);
        }

        $attribute_template = new Attribute_Template($name, $slug, $type, $unit);
        $attribute_template->set_id($id);

        return $attribute_template;
    }

    /**
     * Insert a new attribute template into the database.
     *
     * @since 0.8
     * @param Attribute_Template $attribute_template
     */
    protected function insert(Attribute_Template $attribute_template)
    {
        $term = wp_insert_term(
            $attribute_template->get_name()->get_value(),
            Attribute_Template::TAXONOMY,
            array(
                'slug' => $attribute_template->get_slug()->get_value(),
            )
        );

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to insert the attribute template %s (%s).',
                $attribute_template->get_name()->get_value(),
                $attribute_template->get_slug()->get_value()
            ));
        }

        $attribute_template->set_id(new Attribute_Template_Id($term['term_id']));

        add_term_meta(
            $attribute_template->get_id()->get_value(),
            self::TYPE,
            $attribute_template->get_type()->get_value()
        );

        if(!empty($attribute_template->get_unit())) {
            add_term_meta(
                $attribute_template->get_id()->get_value(),
                self::UNIT,
                $attribute_template->get_unit()->get_value()
            );
        }
    }

    /**
     * Update an existing attribute template in the database.
     *
     * @since 0.8
     * @param Attribute_Template $attribute_template
     */
    protected function update(Attribute_Template $attribute_template)
    {
        $term = wp_update_term(
            $attribute_template->get_id()->get_value(),
            Attribute_Template::TAXONOMY,
            array(
                'name' => $attribute_template->get_name()->get_value(),
                'slug' => $attribute_template->get_slug()->get_value(),
            )
        );

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to update the attribute template %s (%s).',
                $attribute_template->get_name()->get_value(),
                $attribute_template->get_slug()->get_value()
            ));
        }

        if(!empty($term['slug'])) {
            $attribute_template->set_slug(new Slug($term['slug']));
        }

        update_term_meta(
            $attribute_template->get_id()->get_value(),
            self::TYPE,
            $attribute_template->get_type()->get_value()
        );

        if(!empty($attribute_template->get_unit())) {
            update_term_meta(
                $attribute_template->get_id()->get_value(),
                self::UNIT,
                $attribute_template->get_unit()->get_value()
            );
        }
    }
}
