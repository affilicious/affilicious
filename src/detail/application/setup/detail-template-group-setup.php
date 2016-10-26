<?php
namespace Affilicious\Detail\Application\Setup;

use Affilicious\Common\Application\Setup\Setup_Interface;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;
use Affilicious\Detail\Infrastructure\Repository\Carbon\Carbon_Detail_Template_Group_Repository;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail_Template_Group_Setup implements Setup_Interface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        do_action('affilicious_detail_template_group_before_init');

        $singular = __('Detail Template Group', 'affilicious');
        $plural = __('Detail Template Groups', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $singular,
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

        register_post_type(Detail_Template_Group::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => Detail_Template_Group::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=product',
        ));

        do_action('affilicious_detail_template_group_after_init');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        do_action('affilicious_detail_template_group_before_render');

        $carbon_container = Carbon_Container::make('post_meta', __('Detail Templates', 'affilicious'))
            ->show_on_post_type(Detail_Template_Group::POST_TYPE)
            ->add_fields(array(
                Carbon_Field::make('complex', Carbon_Detail_Template_Group_Repository::DETAILS, __('Details', 'affilicious'))
                    //->set_static(true)
                    ->add_fields(array(
                        Carbon_Field::make('text', Carbon_Detail_Template_Group_Repository::DETAIL_TITLE, __('Title', 'affilicious'))
                            ->set_required(true),
                        Carbon_Field::make('select', Carbon_Detail_Template_Group_Repository::DETAIL_TYPE, __('Type', 'affilicious'))
                            ->set_required(true)
                            ->add_options(array(
                                Type::TEXT => __('Text', 'affilicious'),
                                Type::NUMBER => __('Number', 'affilicious'),
                                Type::FILE => __('File', 'affilicious'),
                            )),
                        Carbon_Field::make('text', Carbon_Detail_Template_Group_Repository::DETAIL_UNIT, __('Unit', 'affilicious'))
                            ->set_conditional_logic(array(
                                'relation' => 'and',
                                array(
                                    'field' => Carbon_Detail_Template_Group_Repository::DETAIL_TYPE,
                                    'value' => Type::NUMBER,
                                    'compare' => '=',
                                )
                            )),
                        Carbon_Field::make('text', Carbon_Detail_Template_Group_Repository::DETAIL_HELP_TEXT, __('Help Text', 'affilicious'))
                    ))
                    ->set_header_template('
                        <# if (' . Carbon_Detail_Template_Group_Repository::DETAIL_TITLE . ') { #>
                            {{ ' . Carbon_Detail_Template_Group_Repository::DETAIL_TITLE . ' }}
                        <# } #>
                    ')
            ));

        apply_filters('affilicious_detail_template_group_render_detail_templates_container', $carbon_container);
        do_action('affilicious_detail_template_group_after_render');
    }

    /**
     * Add a column header for the details
     *
     * @since 0.6
     * @param array $defaults
     * @return array
     */
    public function columns_head($defaults)
    {
        $defaults['details'] = __('Details');

        return $defaults;
    }

    /**
     * Add a column for the details
     *
     * @since 0.6
     * @param string $column_name
     * @param int $post_id
     */
    public function columns_content($column_name, $post_id)
    {
        if ($column_name == 'details') {
            $detail_templates = carbon_get_post_meta($post_id, Carbon_Detail_Template_Group_Repository::DETAILS, 'complex');
            if(!empty($detail_templates)) {
                $titles = array_map(function($detail_template) {
                    return $detail_template[Carbon_Detail_Template_Group_Repository::DETAIL_TITLE];
                }, $detail_templates);

                echo implode(', ', $titles);
            }
        }
    }
}
