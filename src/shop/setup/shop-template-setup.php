<?php
namespace Affilicious\Shop\Setup;

use Affilicious\Product\Model\Product;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template_Setup
{
    /**
	 * @hook init
     * @since 0.6
	 */
	public function init()
	{
        do_action('aff_shop_template_before_init');

        $singular = __('Shop Template', 'affilicious');
        $plural = __('Shop Templates', 'affilicious');

        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => __('Shops', 'affilicious'),
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Shop Template', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Shop Template', 'affilicious'), $singular),
            'all_items'             => __('Shops', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Shop Template', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Shop Template', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Shop Template', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Shop Template', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Shop Template', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Shop Template', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Shop Template', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Shop Template', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Shop Template', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Shop Template', 'affilicious'), $plural),
        );

        $args = array(
            'hierarchical'      => false,
            'public'            => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => false,
            'show_in_nav_menus' => false,
            'show_tagcloud'     => false,
            'meta_box_cb'       => false,
            'query_var'         => true,
            'description'       => false,
            'rewrite'           => false,
        );

        $args = apply_filters('aff_shop_template_init_args', $args);

        register_taxonomy(Shop_Template::TAXONOMY, Product::POST_TYPE, $args);

        do_action('aff_shop_template_after_init');
	}
}
