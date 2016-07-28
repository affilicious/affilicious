<?php
namespace Affilicious\ProductsPlugin\Product\Field;

use Affilicious\ProductsPlugin\Product\Product;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class FieldGroupSetup
{
    /**
     * @var FieldGroupFactory
     */
    private $factory;

    /**
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        $this->factory = new FieldGroupFactory();

        add_action('init', array($this, 'init'), 2);
        add_action('init', array($this, 'render'), 3);
    }

    /**
     * Initialize the product field group post type
     */
    public function init()
    {
        $labels = array(
            'name' => _x('Product field groups', 'projektaffiliatetheme'),
            'singular_name' => _x('Product field group', 'projektaffiliatetheme'),
            'menu_name' => __('Product fields', 'projektaffiliatetheme'),
            'name_admin_bar' => __('Product fields', 'projektaffiliatetheme'),
            'archives' => __('Product field group archives', 'projektaffiliatetheme'),
            'parent_item_colon' => __('Parent product field  group:', 'projektaffiliatetheme'),
            'all_items' => __('All product field groups', 'projektaffiliatetheme'),
            'add_new_item' => __('Add new Product group', 'projektaffiliatetheme'),
            'add_new' => __('Add new', 'projektaffiliatetheme'),
            'new_item' => __('New product field group', 'projektaffiliatetheme'),
            'edit_item' => __('Edit product field group', 'projektaffiliatetheme'),
            'update_item' => __('Update product field group', 'projektaffiliatetheme'),
            'view_item' => __('View product field group', 'projektaffiliatetheme'),
            'search_items' => __('Search product field group', 'projektaffiliatetheme'),
            'not_found' => __('Not found', 'projektaffiliatetheme'),
            'not_found_in_trash' => __('Not found in Trash', 'projektaffiliatetheme'),
            'featured_image' => __('Featured Image', 'projektaffiliatetheme'),
            'set_featured_image' => __('Set featured image', 'projektaffiliatetheme'),
            'remove_featured_image' => __('Remove featured image', 'projektaffiliatetheme'),
            'use_featured_image' => __('Use as featured image', 'projektaffiliatetheme'),
            'insert_into_item' => __('Insert into item', 'projektaffiliatetheme'),
            'uploaded_to_this_item' => __('Uploaded to this product field group', 'projektaffiliatetheme'),
            'items_list' => __('Product field groups', 'projektaffiliatetheme'),
            'items_list_navigation' => __('Product field groups navigation', 'projektaffiliatetheme'),
            'filter_items_list' => __('Filter product field groups', 'projektaffiliatetheme'),
        );

        register_post_type(FieldGroup::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => 'dashicons-feedback',
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 6,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => FieldGroup::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => true,
        ));
    }

    /**
     * Render a single product field group post type
     */
    public function render()
    {
        $categories = $this->getProductCategories();

        CarbonContainer::make('post_meta', __('Category', 'projektaffiliatetheme'))
            ->show_on_post_type(FieldGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make("select", FieldGroup::CATEGORY, __("Category", 'projektaffiliatetheme'))
                    ->add_options($categories),
            ));

        CarbonContainer::make('post_meta', __('Fields', 'projektaffiliatetheme'))
            ->show_on_post_type(FieldGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', FieldGroup::FIELDS, __('Fields', 'projektaffiliatetheme'))
                    ->add_fields(array(
                            CarbonField::make('text', Field::CARBON_KEY, __("Field key", 'projektaffiliatetheme'))
                                ->set_required(true)
                                ->help_text(__('Create a unique key with non-special characters, numbers and _ only', 'projektaffiliatetheme')),
                            CarbonField::make("select", Field::CARBON_TYPE, __("Field type", 'projektaffiliatetheme'))
                                ->set_required(true)
                                ->add_options(array(
                                    'text' => __('Text', 'projektaffiliatetheme'),
                                    'number' => __('Number', 'projektaffiliatetheme'),
                                    'file' => __('File', 'projektaffiliatetheme'),
                                )),
                            CarbonField::make('text', Field::CARBON_LABEL, __("Field label", 'projektaffiliatetheme'))
                                ->set_required(true),
                            CarbonField::make('text', Field::CARBON_DEFAULT_VALUE, __("Field default value", 'projektaffiliatetheme'))
                                ->set_conditional_logic(array(
                                    'relation' => 'AND', // Optional, defaults to "AND"
                                    array(
                                        'field' => Field::CARBON_TYPE,
                                        'value' => array('text', 'number'), // Optional, defaults to "". Should be an array if "IN" or "NOT IN" operators are used.
                                        'compare' => 'IN', // Optional, defaults to "=". Available operators: =, <, >, <=, >=, IN, NOT IN
                                    )
                                )),
                            CarbonField::make('text', Field::CARBON_HELP_TEXT, __("Field help text", 'projektaffiliatetheme'))
                        )
                    )
            ));
    }

    /**
     * Get all product categories
     * @return array
     * @throws \Exception
     */
    public function getProductCategories()
    {
        // Get all product categories
        if (is_version('4.5')) {
            $terms = get_terms(array(
                'taxonomy' => Product::TAXONOMY,
                'orderby' => 'name',
                'hide_empty' => false,
                'parent' => 0,
            ));
        } else {
            $terms = get_terms(Product::TAXONOMY, array(
                'orderby' => 'name',
                'hide_empty' => false,
                'parent' => 0,
            ));
        }

        if ($terms instanceof \WP_Error) {
            throw new \Exception('Failed to find the terms for the taxonomy ' . Product::TAXONOMY . '.');
        }

        $categories = array();
        $categories[FieldGroup::CARBON_CATEGORY_NONE] = __('None', 'projektaffiliatetheme');
        foreach ($terms as $term) {
            $categories[$term->slug] = $term->name;
        }

        return $categories;
    }
}
