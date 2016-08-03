<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\Field;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroup;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class FieldGroupSetup implements SetupInterface
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
            'name' => _x('Product Field Groups', 'affiliciousproducts'),
            'singular_name' => _x('Product Field Group', 'affiliciousproducts'),
            'menu_name' => __('Product Field Groups', 'affiliciousproducts'),
            'name_admin_bar' => __('Product Field Groups', 'affiliciousproducts'),
            'archives' => __('Product Field Group Archives', 'affiliciousproducts'),
            'parent_item_colon' => __('Parent Product Field Group:', 'affiliciousproducts'),
            'all_items' => __('Field Groups', 'affiliciousproducts'),
            'add_new_item' => __('Add New Product Field Group', 'affiliciousproducts'),
            'add_new' => __('Add New', 'affiliciousproducts'),
            'new_item' => __('New Product Field Group', 'affiliciousproducts'),
            'edit_item' => __('Edit Product Field Group', 'affiliciousproducts'),
            'update_item' => __('Update Product Field Group', 'affiliciousproducts'),
            'view_item' => __('View Product Field Group', 'affiliciousproducts'),
            'search_items' => __('Search Product Field Group', 'affiliciousproducts'),
            'not_found' => __('Not Found', 'affiliciousproducts'),
            'not_found_in_trash' => __('Not Found In Trash', 'affiliciousproducts'),
            'featured_image' => __('Featured Image', 'affiliciousproducts'),
            'set_featured_image' => __('Set Featured Image', 'affiliciousproducts'),
            'remove_featured_image' => __('Remove Featured Image', 'affiliciousproducts'),
            'use_featured_image' => __('Use As Featured Image', 'affiliciousproducts'),
            'insert_into_item' => __('Insert into item', 'affiliciousproducts'),
            'uploaded_to_this_item' => __('Uploaded To This Product Field Group', 'affiliciousproducts'),
            'items_list' => __('Product Field Groups', 'affiliciousproducts'),
            'items_list_navigation' => __('Product Field Groups Navigation', 'affiliciousproducts'),
            'filter_items_list' => __('Product Filter Field Groups', 'affiliciousproducts'),
        );

        register_post_type(FieldGroup::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => false,
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 4,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => FieldGroup::POST_TYPE,
            'supports' => array('title'),
            'show_in_menu' => 'edit.php?post_type=product',
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
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
}
