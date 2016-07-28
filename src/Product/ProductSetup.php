<?php
namespace Affilicious\ProductsPlugin\Product;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ProductSetup
{
    /**
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        add_action('init', array($this, 'register'), 0);
    }

    /**
     * Register the new product post type
     */
    public function register()
    {
        $labels = array(
            'name' => _x('Products', 'projektaffiliatetheme'),
            'singular_name' => _x('Product', 'projektaffiliatetheme'),
            'menu_name' => __('Products', 'projektaffiliatetheme'),
            'name_admin_bar' => __('Product', 'projektaffiliatetheme'),
            'archives' => __('Item Archives', 'projektaffiliatetheme'),
            'parent_item_colon' => __('Parent Item:', 'projektaffiliatetheme'),
            'all_items' => __('All Products', 'projektaffiliatetheme'),
            'add_new_item' => __('Add New Product', 'projektaffiliatetheme'),
            'add_new' => __('Add New', 'projektaffiliatetheme'),
            'new_item' => __('New Product', 'projektaffiliatetheme'),
            'edit_item' => __('Edit Product', 'projektaffiliatetheme'),
            'update_item' => __('Update Product', 'projektaffiliatetheme'),
            'view_item' => __('View Product', 'projektaffiliatetheme'),
            'search_items' => __('Search Product', 'projektaffiliatetheme'),
            'not_found' => __('Not found', 'projektaffiliatetheme'),
            'not_found_in_trash' => __('Not found in Trash', 'projektaffiliatetheme'),
            'featured_image' => __('Featured Image', 'projektaffiliatetheme'),
            'set_featured_image' => __('Set featured image', 'projektaffiliatetheme'),
            'remove_featured_image' => __('Remove featured image', 'projektaffiliatetheme'),
            'use_featured_image' => __('Use as featured image', 'projektaffiliatetheme'),
            'insert_into_item' => __('Insert into item', 'projektaffiliatetheme'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'projektaffiliatetheme'),
            'items_list' => __('Products list', 'projektaffiliatetheme'),
            'items_list_navigation' => __('Products list navigation', 'projektaffiliatetheme'),
            'filter_items_list' => __('Filter items list', 'projektaffiliatetheme'),
        );

        $args = array(
            'label' => __('Product', 'projektaffiliatetheme'),
            'description' => __('Product Type Description', 'projektaffiliatetheme'),
            'labels' => $labels,
            'menu_icon' => 'dashicons-products',
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions'),
            'taxonomies' => array('product_category'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
        );

        register_post_type('product', $args);

        $labels = array(
            'name' => __('Categories', 'projektaffiliatetheme'),
            'singular_name' => __('Category', 'projektaffiliatetheme'),
            'search_items' => __('Search categories', 'projektaffiliatetheme'),
            'all_items' => __('All categories', 'projektaffiliatetheme'),
            'parent_item' => __('Parent category', 'projektaffiliatetheme'),
            'parent_item_colon' => __('Parent category:', 'projektaffiliatetheme'),
            'edit_item' => __('Edit category', 'projektaffiliatetheme'),
            'update_item' => __('Update category', 'projektaffiliatetheme'),
            'add_new_item' => __('Add New category', 'projektaffiliatetheme'),
            'new_item_name' => __('New category name', 'projektaffiliatetheme'),
            'menu_name' => __('Categories', 'projektaffiliatetheme'),
        );

        register_taxonomy(Product::TAXONOMY, Product::POST_TYPE, array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'category'),
            'public' => true,
        ));
    }
}
