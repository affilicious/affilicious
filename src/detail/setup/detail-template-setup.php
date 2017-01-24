<?php
namespace Affilicious\Detail\Setup;

use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Repository\Carbon\Carbon_Detail_Template_Repository;
use Affilicious\Product\Model\Product;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Setup
{
    /**
     * @since 0.8
     */
    public function init()
    {
        do_action('affilicious_detail_template_setup_before_init');

        $singular = __('Detail Template', 'affilicious');
        $plural = __('Detail Templates', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => __('Details', 'affilicious'),
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Detail Template', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Detail Template', 'affilicious'), $singular),
            'all_items'             => __('Details', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Detail Template', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Detail Template', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Detail Template', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Detail Template', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Detail Template', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Detail Template', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Detail Template', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Detail Template', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Detail Template', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Detail Template', 'affilicious'), $plural),
        );

        register_taxonomy(Detail_Template::TAXONOMY,  Product::POST_TYPE, array(
            'hierarchical'      => false,
            'public'            => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'meta_box_cb'       => false,
            'query_var'         => true,
            'description'       => false,
            'rewrite'           => false,
        ));

        do_action('affilicious_detail_template_setup_after_init');
    }

    /**
     * @since 0.8
     */
    public function render()
    {
        do_action('affilicious_detail_template_setup_before_render');

        $carbon_container = Carbon_Container::make('term_meta', __('Detail Template', 'affilicious'))
            ->show_on_taxonomy(Detail_Template::TAXONOMY)
            ->add_fields(array(
                Carbon_Field::make('select', Carbon_Detail_Template_Repository::TYPE, __('Type', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        Type::TEXT => __('Text', 'affilicious'),
                        Type::NUMBER => __('Number', 'affilicious'),
                        Type::FILE => __('File', 'affilicious'),
                    )),
                Carbon_Field::make('text', Carbon_Detail_Template_Repository::UNIT, __('Unit', 'affilicious'))
                    ->set_conditional_logic(array(
                        'relation' => 'and',
                        array(
                            'field' => Carbon_Detail_Template_Repository::TYPE,
                            'value' => Type::NUMBER,
                            'compare' => '=',
                        )
                    )),
            ));

        apply_filters('affilicious_detail_template_setup_render_detail_templates_container', $carbon_container);
        do_action('affilicious_detail_template_setup_after_render');
    }
}
