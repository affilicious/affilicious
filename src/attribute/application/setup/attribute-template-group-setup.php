<?php
namespace Affilicious\Attribute\Application\Setup;

use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Attribute\Infrastructure\Repository\Carbon\Carbon_Attribute_Template_Group_Repository;
use Affilicious\Common\Application\Setup\Setup_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Group_Setup implements Setup_Interface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        do_action('affilicious_attribute_template_group_before_init');

        $singular = __('Attribute Template Group', 'affilicious');
        $plural = __('Attribute Template Groups', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $singular,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Attribute Template', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Attribute Template', 'affilicious'), $singular),
            'all_items'             => __('Attributes', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Attribute Template', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Attribute Template', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Attribute Template', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Attribute Template', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Attribute Template', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Attribute Template', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Attribute Template', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Attribute Template', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Attribute Template', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Attribute Template', 'affilicious'), $plural),
        );

        register_post_type(Attribute_Template_Group::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => Attribute_Template_Group::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=aff_product',
        ));

        do_action('affilicious_attribute_template_group_after_init');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        do_action('affilicious_attribute_template_group_before_render');

        $carbon_container = Carbon_Container::make('post_meta', __('Attribute Templates', 'affilicious'))
            ->show_on_post_type(Attribute_Template_Group::POST_TYPE)
            ->add_fields(array(
                Carbon_Field::make('complex', Carbon_Attribute_Template_Group_Repository::ATTRIBUTES, __('Attributes', 'affilicious'))
                    ->set_max(3)
                    ->add_fields(array(
                        Carbon_Field::make('text', Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TITLE, __('Title', 'affilicious'))
                            ->set_required(true),
                        Carbon_Field::make('select', Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TYPE, __('Type', 'affilicious'))
                            ->set_required(true)
                            ->add_options(array(
                                Type::TEXT => __('Text', 'affilicious'),
                                Type::NUMBER => __('Number', 'affilicious'),
                            )),
                        Carbon_Field::make('text', Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_UNIT, __('Unit', 'affilicious'))
                            ->set_conditional_logic(array(
                                'relation' => 'and',
                                array(
                                    'field' => Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TYPE,
                                    'value' => Type::NUMBER,
                                    'compare' => '=',
                                )
                            )),
                        Carbon_Field::make('text', Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_HELP_TEXT, __('Help Text', 'affilicious'))
                    ))
                    ->set_header_template('
                        <# if (' . Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TITLE . ') { #>
                            {{ ' . Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TITLE . ' }}
                        <# } #>
                    ')
            ));

        apply_filters('affilicious_attribute_template_group_render_attribute_templates_container', $carbon_container);
        do_action('affilicious_attribute_template_group_after_render');
    }

    /**
     * Add a column header for the attributes
     *
     * @since 0.6
     * @param array $defaults
     * @return array
     */
    public function columns_head($defaults)
    {
        $defaults['attributes'] = __('Attributes');

        return $defaults;
    }

    /**
     * Add a column for the attributes
     *
     * @since 0.6
     * @param string $column_name
     * @param int $post_id
     */
    public function columns_content($column_name, $post_id)
    {
        if ($column_name == 'attributes') {
            $detail_templates = carbon_get_post_meta($post_id, Carbon_Attribute_Template_Group_Repository::ATTRIBUTES, 'complex');
            if(!empty($detail_templates)) {
                $titles = array_map(function($detail_template) {
                    return $detail_template[Carbon_Attribute_Template_Group_Repository::ATTRIBUTE_TITLE];
                }, $detail_templates);

                echo implode(', ', $titles);
            }
        }
    }
}
