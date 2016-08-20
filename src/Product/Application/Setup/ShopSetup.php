<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;
use Affilicious\ProductsPlugin\Product\Domain\Model\ShopRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ShopSetup implements SetupInterface
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name' => __('Shops', 'affilicious-products'),
            'singular_name' => __('Shop', 'affilicious-products'),
            'menu_name' => __('Shop', 'affilicious-products'),
            'name_admin_bar' => __('Shop', 'affilicious-products'),
            'archives' => __('Shop Archives', 'affilicious-products'),
            'parent_item_colon' => __('Parent Shop:', 'affilicious-products'),
            'all_items' => __('Shops', 'affilicious-products'),
            'add_new_item' => __('Add New Shop', 'affilicious-products'),
            'add_new' => __('Add New', 'affilicious-products'),
            'new_item' => __('New Shop', 'affilicious-products'),
            'edit_item' => __('Edit Shop', 'affilicious-products'),
            'update_item' => __('Update Shop', 'affilicious-products'),
            'view_item' => __('View Shop', 'affilicious-products'),
            'search_items' => __('Search Shop', 'affilicious-products'),
            'not_found' => __('Not Found', 'affilicious-products'),
            'not_found_in_trash' => __('Not Found In Trash', 'affilicious-products'),
            'featured_image' => __('Logo', 'affilicious-products'),
            'set_featured_image' => __('Set Logo', 'affilicious-products'),
            'remove_featured_image' => __('Remove Logo', 'affilicious-products'),
            'use_featured_image' => __('Use As Logo', 'affilicious-products'),
            'insert_into_item' => __('Insert Into Shop', 'affilicious-products'),
            'uploaded_to_this_item' => __('Uploaded To This Shop', 'affilicious-products'),
            'items_list' => __('Shop', 'affilicious-products'),
            'items_list_navigation' => __('Shop Navigation', 'affilicious-products'),
            'filter_items_list' => __('Filter Shops', 'affilicious-products'),
        );

        register_post_type(Shop::POST_TYPE, array(
            'labels' => $labels,
            'public' => false,
            'menu_icon' => 'dashicons-cart',
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
            '_builtin' => false,
            'menu_position' => 5,
            'capability_type' => 'page',
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => Shop::POST_TYPE,
            'show_in_menu' => 'edit.php?post_type=product',
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
    }

    /**
     * Add a column header for the logo
     * @param array $defaults
     * @return array
     */
    public function columnsHead($defaults)
    {
        $new = array();
        foreach ($defaults as $key => $title) {
            // Put the logo column before the date column
            if ($key == 'date') {
                $new['logo'] = __('Logo', 'affilicious-products');
            }
            $new[$key] = $title;
        }
        return $new;
    }

    /**
     * Add a column for the logo
     * @param string $columnName
     * @param int $shopId
     */
    public function columnsContent($columnName, $shopId)
    {
        if ($columnName == 'logo') {
            $shopLogo = $this->getLogo($shopId);
            if ($shopLogo) {
                echo '<img src="' . $shopLogo . '" />';
            }
        }
    }

    /**
     * Get the logo by the shop ID
     * @param int $shopId
     * @return null|string
     */
    private function getLogo($shopId)
    {
        $shopLogoId = get_post_thumbnail_id($shopId);
        if (!$shopLogoId) {
            return null;
        }

        $shopLogo = wp_get_attachment_image_src($shopLogoId, 'featured_preview');
        return $shopLogo[0];
    }
}
