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

if (!defined('ABSPATH')) {
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
        $attribute_template_id = $attribute_template->has_id() ?
            $this->update($attribute_template) :
            $this->insert($attribute_template);

        return $attribute_template_id;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Attribute_Template_Id $attribute_template_id)
    {
        $attribute_template = $this->find_one_by_id($attribute_template_id);
        if($attribute_template === null) {
            return new \WP_Error('aff_attribute_template_not_found', sprintf(
                'Attribute template #%s not found in the database.',
                $attribute_template_id->get_value()
            ));
        }

        $result = wp_delete_term(
            $attribute_template_id->get_value(),
            Attribute_Template::TAXONOMY
        );

        if($result === 0) {
            return new \WP_Error('aff_invalid_deletion_of_default_category', sprintf(
                "It's not allowed to delete a default Wordpress category",
                $attribute_template_id->get_value()
            ));
        }

        if($result instanceof \WP_Error) {
            return $result;
        }

        $attribute_template->set_id(null);

        return $attribute_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_one_by_id(Attribute_Template_Id $attribute_template_id)
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
    public function find_one_by_name(Name $name)
    {
        $term = get_term_by('name', $name->get_value(), Attribute_Template::TAXONOMY);
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
    public function find_one_by_slug(Slug $slug)
    {
        $term = get_term_by('slug', $slug->get_value(), Attribute_Template::TAXONOMY);
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
            $attribute_template = $this->find_one_by_id($attribute_template_id);
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
    public function find_all($args = array())
    {
        $args['taxonomy'] = Attribute_Template::TAXONOMY;
        $args = wp_parse_args($args, array(
            'hide_empty' => false
        ));

        $terms = get_terms($args);
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
     * @return Attribute_Template_Id|\WP_Error
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

        if(empty($term)) {
            return new \WP_Error('aff_attribute_template_not_stored', sprintf(
                'Failed to store the attribute template #%s (%s) into the database.',
                $attribute_template->get_id()->get_value(),
                $attribute_template->get_name()->get_value()
            ));
        }

        if($term instanceof \WP_Error) {
            return $term;
        }

        $attribute_template->set_id(new Attribute_Template_Id($term['term_id']));

        $result = add_term_meta(
            $attribute_template->get_id()->get_value(),
            self::TYPE,
            $attribute_template->get_type()->get_value()
        );

        if($result instanceof \WP_Error) {
            return $result;
        }

        if($attribute_template->has_unit()) {
            add_term_meta(
                $attribute_template->get_id()->get_value(),
                self::UNIT,
                $attribute_template->get_unit()->get_value()
            );
        }

        return $attribute_template->get_id();
    }

    /**
     * Update an existing attribute template in the database.
     *
     * @since 0.8
     * @param Attribute_Template $attribute_template
     * @return Attribute_Template_Id|\WP_Error
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

        if(empty($term)) {
            return new \WP_Error('aff_attribute_template_not_stored', sprintf(
                'Failed to store the attribute template #%s (%s) into the database.',
                $attribute_template->get_id()->get_value(),
                $attribute_template->get_name()->get_value()
            ));
        }

        if($term instanceof \WP_Error) {
            return $term;
        }

        if(!empty($term['slug'])) {
            $attribute_template->set_slug(new Slug($term['slug']));
        }

        $result = update_term_meta(
            $attribute_template->get_id()->get_value(),
            self::TYPE,
            $attribute_template->get_type()->get_value()
        );

        if($result instanceof \WP_Error) {
            return $result;
        }

        if($attribute_template->has_unit()) {
            update_term_meta(
                $attribute_template->get_id()->get_value(),
                self::UNIT,
                $attribute_template->get_unit()->get_value()
            );
        }

        return $attribute_template->get_id();
    }
}
