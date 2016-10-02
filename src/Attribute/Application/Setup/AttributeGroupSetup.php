<?php
namespace Affilicious\Attribute\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Attribute\Infrastructure\Persistence\Carbon\CarbonAttributeGroupRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeGroupSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name'                  => __('Attribute Groups', 'affilicious'),
            'singular_name'         => __('Attribute Group', 'affilicious'),
            'menu_name'             => __('Attribute Groups', 'affilicious'),
            'name_admin_bar'        => __('Attribute Groups', 'affilicious'),
            'archives'              => __('Attribute Group Archives', 'affilicious'),
            'parent_item_colon'     => __('Parent Attribute Group:', 'affilicious'),
            'all_items'             => __('Attributes', 'affilicious'),
            'add_new_item'          => __('Add New Attribute Group', 'affilicious'),
            'add_new'               => __('Add New', 'affilicious'),
            'new_item'              => __('New Attribute Group', 'affilicious'),
            'edit_item'             => __('Edit Attribute Group', 'affilicious'),
            'update_item'           => __('Update Attribute Group', 'affilicious'),
            'view_item'             => __('View Attribute Group', 'affilicious'),
            'search_items'          => __('Search Attribute Group', 'affilicious'),
            'not_found'             => __('Not Found', 'affilicious'),
            'not_found_in_trash'    => __('Not Found In Trash', 'affilicious'),
            'featured_image'        => __('Featured Image', 'affilicious'),
            'set_featured_image'    => __('Set Featured Image', 'affilicious'),
            'remove_featured_image' => __('Remove Featured Image', 'affilicious'),
            'use_featured_image'    => __('Use As Featured Image', 'affilicious'),
            'insert_into_item'      => __('Insert into item', 'affilicious'),
            'uploaded_to_this_item' => __('Uploaded To This Attribute Group', 'affilicious'),
            'items_list'            => __('Attribute Groups', 'affilicious'),
            'items_list_navigation' => __('Attribute Groups Navigation', 'affilicious'),
            'filter_items_list'     => __('Filter Attribute Groups', 'affilicious'),
        );

        register_post_type(AttributeGroup::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => AttributeGroup::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=product',
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $carbonContainer = CarbonContainer::make('post_meta', __('Attributes', 'affilicious'))
            ->show_on_post_type(AttributeGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', CarbonAttributeGroupRepository::ATTRIBUTES, __('Attributes', 'affilicious'))
                    ->add_fields(array(
                        CarbonField::make('text', CarbonAttributeGroupRepository::ATTRIBUTE_TITLE, __('Title', 'affilicious'))
                            ->set_required(true),
                        CarbonField::make('select', CarbonAttributeGroupRepository::ATTRIBUTE_TYPE, __('Type', 'affilicious'))
                            ->set_required(true)
                            ->add_options(array(
                                Type::TEXT => __('Text', 'affilicious'),
                                Type::NUMBER => __('Number', 'affilicious'),
                            )),
                        CarbonField::make('text', CarbonAttributeGroupRepository::ATTRIBUTE_VALUE, __('Value', 'affilicious')),
                        CarbonField::make('text', CarbonAttributeGroupRepository::ATTRIBUTE_HELP_TEXT, __('Help Text', 'affilicious'))
                    ))
                    ->set_header_template('
                        <# if (' . CarbonAttributeGroupRepository::ATTRIBUTE_TITLE . ') { #>
                            {{ ' . CarbonAttributeGroupRepository::ATTRIBUTE_TITLE . ' }}
                        <# } #>
                    ')
            ));

        apply_filters('affilicious_detail_groups_render_fields', $carbonContainer);
    }
}
