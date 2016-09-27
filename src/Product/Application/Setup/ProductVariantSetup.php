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
        $labels = array(
            'name' => __('Product Variants', 'affilicious'),
            'singular_name' => __('Product Variant', 'affilicious'),
            'menu_name' => __('Product Variants', 'affilicious'),
            'all_items' => __('All Product Variants', 'affilicious'),
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
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
    }
}
