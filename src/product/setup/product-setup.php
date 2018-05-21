<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.6
 */
class Product_Setup
{
    /**
     * @hook init
     * @since 0.6
     */
    public function init()
    {
        do_action('aff_product_before_init');

        $singular = __('Product', 'affilicious');
        $plural = __('Products', 'affilicious');

        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $plural,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Product', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Product', 'affilicious'), $singular),
            'all_items'             => $plural,
            'add_new_item'          => sprintf(_x('Add New %s', 'Product', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Product', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Product', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Product', 'affilicious'), $singular),
            'view_items'            => sprintf(_x('View %s', 'Product', 'affilicious'), $plural),
            'view_item'             => sprintf(_x('View %s', 'Product', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Product', 'affilicious'), $singular),
            'not_found'             => sprintf(_x('No %s found', 'Product', 'affilicious'), $plural),
            'not_found_in_trash'    => sprintf(_x('No %s found in trash', 'Product', 'affilicious'), $plural),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Product', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Product', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Product', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Product', 'affilicious'), $plural),
        );

	    $slug = carbon_get_theme_option('affilicious_options_product_container_general_tab_slug_field');
	    if(empty($slug)) {
	    	$slug = Product::SLUG;
	    }

        $args = array(
            'label' => $singular,
            'labels' => $labels,
            'menu_icon' => 'dashicons-products',
            'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions'),
            'rewrite' => array('slug' => $slug),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'hierarchical' => false,
            'can_export' => true,
            'has_archive' => true,
            'capability_type' => 'page',
            'rest_base' => 'aff-products',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

	    $args = apply_filters('aff_product_init_args', $args);
        register_post_type(Product::POST_TYPE, $args);

        do_action('aff_product_after_init');
    }
}
