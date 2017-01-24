<?php
namespace Affilicious\Shop\Repository\Carbon;

use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Model\Shop_Template_Id;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Shop_Template_Repository extends Abstract_Carbon_Repository implements Shop_Template_Repository_Interface
{
    const PROVIDER = '_affilicious_shop_template_provider';
    const THUMBNAIL = '_affilicious_shop_template_thumbnail';

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store(Shop_Template $shop_template)
    {
        $shop_template->has_id() ? $this->update($shop_template) : $this->insert($shop_template);

        add_action('affilicious_shop_template_repository_store', $shop_template);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function store_all($shop_templates)
    {
        Assert::allIsInstanceOf($shop_templates, Shop_Template::class);

        foreach ($shop_templates as $shop_template) {
            $this->store($shop_template);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete(Shop_Template_Id $shop_template_id)
    {
        wp_delete_term(
            $shop_template_id->get_value(),
            Shop_Template::TAXONOMY
        );

        add_action('affilicious_shop_template_repository_delete', $shop_template_id);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete_all($shop_template_ids)
    {
        Assert::allIsInstanceOf($shop_template_ids, Shop_Template_Id::class);

        foreach ($shop_template_ids as $shop_template_id) {
            $this->delete($shop_template_id);
        }
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_by_id(Shop_Template_Id $shop_template_id)
    {
        $term = get_term($shop_template_id->get_value(), Shop_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $shop_template = $this->build($term);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_one_by_slug(Slug $slug)
    {
        $term = get_term_by('slug', $slug->get_value(), Shop_Template::TAXONOMY);
        if (empty($term) || $term instanceof \WP_Error) {
            return null;
        }

        $shop_template = $this->build($term);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all_by_id($shop_template_ids)
    {
        Assert::allIsInstanceOf($shop_template_ids, Shop_Template_Id::class);

        $shop_templates = array();

        foreach ($shop_template_ids as $shop_template_id) {
            $shop_template = $this->find_by_id($shop_template_id);
            if($shop_template === null) {
                $shop_templates[] = $shop_template;
            }
        }

        return $shop_templates;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function find_all()
    {
        $terms = get_terms(array(
            'taxonomy' => Shop_Template::TAXONOMY,
            'hide_empty' => false
        ));

        if(empty($terms) || $terms instanceof \WP_Error) {
            return array();
        }

        $shop_templates = array();
        foreach ($terms as $term) {
            $shop_template = $this->build($term);
            if($shop_template !== null) {
                $shop_templates[] = $shop_template;
            }
        }

        return $shop_templates;
    }

    /**
     * Build the shop template from the term.
     *
     * @since 0.8
     * @param \WP_Term $term
     * @return Shop_Template
     */
    protected function build(\WP_Term $term)
    {
        $id = new Shop_Template_Id($term->term_id);
        $name = new Name($term->name);
        $slug = new Slug($term->slug);
        $thumbnail_id = null;
        $provider_id = null;

        $shop_template = new Shop_Template($name, $slug);
        $shop_template->set_id($id);

        if($raw_thumbnail_id = carbon_get_term_meta($id->get_value(), self::THUMBNAIL)) {
            $thumbnail_id = new Image_Id($raw_thumbnail_id);
            $shop_template->set_thumbnail_id($thumbnail_id);
        }

        if($raw_provider_id = carbon_get_term_meta($id->get_value(), self::PROVIDER)) {
            $provider_id = new Provider_Id($raw_provider_id);
            $shop_template->set_provider_id($provider_id);
        }

        return $shop_template;
    }

    /**
     * Insert a new shop template into the database.
     *
     * @since 0.8
     * @param Shop_Template $shop_template
     */
    protected function insert(Shop_Template $shop_template)
    {
        $term = wp_insert_term(
            $shop_template->get_name()->get_value(),
            Shop_Template::TAXONOMY,
            array(
                'slug' => $shop_template->get_slug()->get_value(),
            )
        );

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to insert the shop template %s (%s).',
                $shop_template->get_name()->get_value(),
                $shop_template->get_slug()->get_value()
            ));
        }

        $shop_template->set_id(new Shop_Template_Id($term['term_id']));

        if($shop_template->has_thumbnail_id()) {
            add_term_meta(
                $shop_template->get_id()->get_value(),
                self::THUMBNAIL,
                $shop_template->get_thumbnail_id()->get_value()
            );
        }

        if($shop_template->has_provider_id()) {
            add_term_meta(
                $shop_template->get_id()->get_value(),
                self::PROVIDER,
                $shop_template->get_provider_id()->get_value()
            );
        }
    }

    /**
     * Update an existing shop template in the database.
     *
     * @since 0.8
     * @param Shop_Template $shop_template
     */
    protected function update(Shop_Template $shop_template)
    {
        $term = wp_update_term(
            $shop_template->get_id()->get_value(),
            Shop_Template::TAXONOMY,
            array(
                'name' => $shop_template->get_name()->get_value(),
                'slug' => $shop_template->get_slug()->get_value(),
            )
        );

        if(empty($term) || $term instanceof \WP_Error) {
            throw new \RuntimeException(sprintf(
                'Failed to update the shop template %s (%s).',
                $shop_template->get_name()->get_value(),
                $shop_template->get_slug()->get_value()
            ));
        }

        if(!empty($term['slug'])) {
            $shop_template->set_slug(new Slug($term['slug']));
        }

        if($shop_template->has_thumbnail_id()) {
            update_term_meta(
                $shop_template->get_id()->get_value(),
                self::THUMBNAIL,
                $shop_template->get_thumbnail_id()->get_value()
            );
        }

        if($shop_template->has_provider_id()) {
            update_term_meta(
                $shop_template->get_id()->get_value(),
                self::PROVIDER,
                $shop_template->get_provider_id()->get_value()
            );
        }
    }
}
