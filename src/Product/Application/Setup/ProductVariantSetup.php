<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductVariantSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        do_action('affilicious_product_before_init');

        $singular = __('Product Variant', 'affilicious');
        $plural = __('Product Variants', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $singular,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Product Variant', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Product Variant', 'affilicious'), $singular),
            'all_items'             => __('Product Variants', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Product Variant', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Product Variant', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Product Variant', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Product Variant', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Product Variant', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Product Variant', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Product Variant', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded To This %s', 'Product Variant', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Product Variant', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Product Variant', 'affilicious'), $plural),
        );

        $args = array(
            'label' => __('Product Variant', 'affilicious'),
            'labels' => $labels,
            'menu_icon' => false,
            'supports' => array('title', 'thumbnail'),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => false,
            'menu_position' => 0,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_menu'    => 'edit.php?post_type=product',
        );

        register_post_type(ProductVariant::POST_TYPE, $args);

        do_action('affilicious_product_after_init');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        do_action('affilicious_product_before_render');

        // Nothing to do here

        do_action('affilicious_product_after_render');
    }
}
