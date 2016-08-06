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
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 2);
        add_action('init', array($this, 'render'), 3);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name' => _x('Product Detail Groups', 'affiliciousproducts'),
            'singular_name' => _x('Product Detail Group', 'affiliciousproducts'),
            'menu_name' => __('Product Detail Groups', 'affiliciousproducts'),
            'name_admin_bar' => __('Product Detail Groups', 'affiliciousproducts'),
            'archives' => __('Product Detail Group Archives', 'affiliciousproducts'),
            'parent_item_colon' => __('Parent Product Detail Group:', 'affiliciousproducts'),
            'all_items' => __('Detail Groups', 'affiliciousproducts'),
            'add_new_item' => __('Add New Product Detail Group', 'affiliciousproducts'),
            'add_new' => __('Add New', 'affiliciousproducts'),
            'new_item' => __('New Product Detail Group', 'affiliciousproducts'),
            'edit_item' => __('Edit Product Detail Group', 'affiliciousproducts'),
            'update_item' => __('Update Product Detail Group', 'affiliciousproducts'),
            'view_item' => __('View Product Detail Group', 'affiliciousproducts'),
            'search_items' => __('Search Product Detail Group', 'affiliciousproducts'),
            'not_found' => __('Not Found', 'affiliciousproducts'),
            'not_found_in_trash' => __('Not Found In Trash', 'affiliciousproducts'),
            'featured_image' => __('Featured Image', 'affiliciousproducts'),
            'set_featured_image' => __('Set Featured Image', 'affiliciousproducts'),
            'remove_featured_image' => __('Remove Featured Image', 'affiliciousproducts'),
            'use_featured_image' => __('Use As Featured Image', 'affiliciousproducts'),
            'insert_into_item' => __('Insert into item', 'affiliciousproducts'),
            'uploaded_to_this_item' => __('Uploaded To This Product Detail Group', 'affiliciousproducts'),
            'items_list' => __('Product Detail Groups', 'affiliciousproducts'),
            'items_list_navigation' => __('Product Detail Groups Navigation', 'affiliciousproducts'),
            'filter_items_list' => __('Product Filter Detail Groups', 'affiliciousproducts'),
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
        CarbonContainer::make('post_meta', __('Detail Setup', 'affiliciousproducts'))
            ->show_on_post_type(DetailGroup::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', CarbonDetailGroupRepository::CARBON_DETAILS, __('Details', 'affiliciousproducts'))
                    ->add_fields(array(
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_KEY, __('Key', 'affiliciousproducts'))
                                ->set_required(true)
                                ->help_text(__('Create a unique key with non-special characters, numbers and _ only', 'affiliciousproducts')),
                            CarbonField::make('select', CarbonDetailGroupRepository::CARBON_DETAIL_TYPE, __('Type', 'affiliciousproducts'))
                                ->set_required(true)
                                ->add_options(array(
                                    DetailGroup::DETAIL_TYPE_TEXT => __('Text', 'affiliciousproducts'),
                                    DetailGroup::DETAIL_TYPE_NUMBER => __('Number', 'affiliciousproducts'),
                                    DetailGroup::DETAIL_TYPE_FILE => __('File', 'affiliciousproducts'),
                                )),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_LABEL, __('Label', 'affiliciousproducts'))
                                ->set_required(true),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_DEFAULT_VALUE, __('Default Value', 'affiliciousproducts'))
                                ->set_conditional_logic(array(
                                    'relation' => 'AND',
                                    array(
                                        'field' => CarbonDetailGroupRepository::CARBON_DETAIL_TYPE,
                                        'value' => array(DetailGroup::DETAIL_TYPE_TEXT, DetailGroup::DETAIL_TYPE_NUMBER),
                                        'compare' => 'IN',
                                    )
                                )),
                            CarbonField::make('text', CarbonDetailGroupRepository::CARBON_DETAIL_HELP_TEXT, __('Help Text', 'affiliciousproducts'))
                        )
                    )
            ));
    }
}
