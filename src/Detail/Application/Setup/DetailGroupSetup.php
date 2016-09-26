<?php
namespace Affilicious\Detail\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Detail\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailGroupSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name'                  => __('Detail Groups', 'affilicious'),
            'singular_name'         => __('Detail Group', 'affilicious'),
            'menu_name'             => __('Detail Groups', 'affilicious'),
            'name_admin_bar'        => __('Detail Groups', 'affilicious'),
            'archives'              => __('Detail Group Archives', 'affilicious'),
            'parent_item_colon'     => __('Parent Detail Group:', 'affilicious'),
            'all_items'             => __('Details', 'affilicious'),
            'add_new_item'          => __('Add New Detail Group', 'affilicious'),
            'add_new'               => __('Add New', 'affilicious'),
            'new_item'              => __('New Detail Group', 'affilicious'),
            'edit_item'             => __('Edit Detail Group', 'affilicious'),
            'update_item'           => __('Update Detail Group', 'affilicious'),
            'view_item'             => __('View Detail Group', 'affilicious'),
            'search_items'          => __('Search Detail Group', 'affilicious'),
            'not_found'             => __('Not Found', 'affilicious'),
            'not_found_in_trash'    => __('Not Found In Trash', 'affilicious'),
            'featured_image'        => __('Featured Image', 'affilicious'),
            'set_featured_image'    => __('Set Featured Image', 'affilicious'),
            'remove_featured_image' => __('Remove Featured Image', 'affilicious'),
            'use_featured_image'    => __('Use As Featured Image', 'affilicious'),
            'insert_into_item'      => __('Insert into item', 'affilicious'),
            'uploaded_to_this_item' => __('Uploaded To This Detail Group', 'affilicious'),
            'items_list'            => __('Detail Groups', 'affilicious'),
            'items_list_navigation' => __('Detail Groups Navigation', 'affilicious'),
            'filter_items_list'     => __('Filter Detail Groups', 'affilicious'),
        );

        register_post_type(DetailGroup::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => DetailGroup::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=product',
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $carbonContainer = CarbonContainer::make('post_meta', __('Fields', 'affilicious'))
            ->show_on_post_type(DetailGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', CarbonDetailGroupRepository::DETAILS, __('Details', 'affilicious'))
                    ->add_fields(array(
                        CarbonField::make('text', CarbonDetailGroupRepository::DETAIL_NAME, __('Name', 'affilicious'))
                            ->set_required(true),
                        CarbonField::make('select', CarbonDetailGroupRepository::DETAIL_TYPE, __('Type', 'affilicious'))
                            ->set_required(true)
                            ->add_options(array(
                                Type::TEXT => __('Text', 'affilicious'),
                                Type::NUMBER => __('Number', 'affilicious'),
                                Type::FILE => __('File', 'affilicious'),
                            )),
                        CarbonField::make('text', CarbonDetailGroupRepository::DETAIL_UNIT, __('Unit', 'affilicious'))
                            ->set_conditional_logic(array(
                                'relation' => 'AND',
                                array(
                                    'field' => CarbonDetailGroupRepository::DETAIL_TYPE,
                                    'value' => array(Type::TEXT, Type::NUMBER),
                                    'compare' => 'IN',
                                )
                            )),
                        CarbonField::make('text', CarbonDetailGroupRepository::DETAIL_HELP_TEXT, __('Help Text', 'affilicious'))
                    ))
                    ->set_header_template('
                        <# if (' . CarbonDetailGroupRepository::DETAIL_NAME . ') { #>
                            {{ ' . CarbonDetailGroupRepository::DETAIL_NAME . ' }}
                        <# } #>
                    ')
            ));

        apply_filters('affilicious_detail_groups_render_fields', $carbonContainer);
    }
}
