<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroup;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class DetailGroupSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name' => __('Detail Groups', 'affilicious-products'),
            'singular_name' => __('Detail Group', 'affilicious-products'),
            'menu_name' => __('Detail Groups', 'affilicious-products'),
            'name_admin_bar' => __('Detail Groups', 'affilicious-products'),
            'archives' => __('Detail Group Archives', 'affilicious-products'),
            'parent_item_colon' => __('Parent Detail Group:', 'affilicious-products'),
            'all_items' => __('Detail Groups', 'affilicious-products'),
            'add_new_item' => __('Add New Detail Group', 'affilicious-products'),
            'add_new' => __('Add New', 'affilicious-products'),
            'new_item' => __('New Detail Group', 'affilicious-products'),
            'edit_item' => __('Edit Detail Group', 'affilicious-products'),
            'update_item' => __('Update Detail Group', 'affilicious-products'),
            'view_item' => __('View Detail Group', 'affilicious-products'),
            'search_items' => __('Search Detail Group', 'affilicious-products'),
            'not_found' => __('Not Found', 'affilicious-products'),
            'not_found_in_trash' => __('Not Found In Trash', 'affilicious-products'),
            'featured_image' => __('Featured Image', 'affilicious-products'),
            'set_featured_image' => __('Set Featured Image', 'affilicious-products'),
            'remove_featured_image' => __('Remove Featured Image', 'affilicious-products'),
            'use_featured_image' => __('Use As Featured Image', 'affilicious-products'),
            'insert_into_item' => __('Insert into item', 'affilicious-products'),
            'uploaded_to_this_item' => __('Uploaded To This Detail Group', 'affilicious-products'),
            'items_list' => __('Detail Groups', 'affilicious-products'),
            'items_list_navigation' => __('Detail Groups Navigation', 'affilicious-products'),
            'filter_items_list' => __('Filter Detail Groups', 'affilicious-products'),
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
        CarbonContainer::make('post_meta', __('Detail Setup', 'affilicious-products'))
            ->show_on_post_type(DetailGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', CarbonDetailGroupRepository::CARBON_DETAILS, __('Details', 'affilicious-products'))
                    ->add_fields(array(
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_KEY, __('Key', 'affilicious-products'))
                                ->set_required(true)
                                ->help_text(__('Create a unique key with non-special characters, numbers, -, @ and _ only (e.g @unique-key_here).', 'affilicious-products')),
                            CarbonField::make('select', CarbonDetailGroupRepository::CARBON_DETAIL_TYPE, __('Type', 'affilicious-products'))
                                ->set_required(true)
                                ->add_options(array(
                                    DetailGroup::DETAIL_TYPE_TEXT => __('Text', 'affilicious-products'),
                                    DetailGroup::DETAIL_TYPE_NUMBER => __('Number', 'affilicious-products'),
                                    DetailGroup::DETAIL_TYPE_FILE => __('File', 'affilicious-products'),
                                )),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_LABEL, __('Label', 'affilicious-products'))
                                ->set_required(true),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_DEFAULT_VALUE, __('Default Value', 'affilicious-products'))
                                ->set_conditional_logic(array(
                                    'relation' => 'AND',
                                    array(
                                        'field' => CarbonDetailGroupRepository::CARBON_DETAIL_TYPE,
                                        'value' => array(DetailGroup::DETAIL_TYPE_TEXT, DetailGroup::DETAIL_TYPE_NUMBER),
                                        'compare' => 'IN',
                                    )
                                )),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_HELP_TEXT, __('Help Text', 'affilicious-products'))
                        )
                    )
            ));
    }
}
