<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Shop;
use Affilicious\Product\Domain\Model\ShopRepositoryInterface;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
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
            'name' => __('Shops', 'affilicious'),
            'singular_name' => __('Shop', 'affilicious'),
            'menu_name' => __('Shop', 'affilicious'),
            'name_admin_bar' => __('Shop', 'affilicious'),
            'archives' => __('Shop Archives', 'affilicious'),
            'parent_item_colon' => __('Parent Shop:', 'affilicious'),
            'all_items' => __('Shops', 'affilicious'),
            'add_new_item' => __('Add New Shop', 'affilicious'),
            'add_new' => __('Add New', 'affilicious'),
            'new_item' => __('New Shop', 'affilicious'),
            'edit_item' => __('Edit Shop', 'affilicious'),
            'update_item' => __('Update Shop', 'affilicious'),
            'view_item' => __('View Shop', 'affilicious'),
            'search_items' => __('Search Shop', 'affilicious'),
            'not_found' => __('Not Found', 'affilicious'),
            'not_found_in_trash' => __('Not Found In Trash', 'affilicious'),
            'featured_image' => __('Logo', 'affilicious'),
            'set_featured_image' => __('Set Logo', 'affilicious'),
            'remove_featured_image' => __('Remove Logo', 'affilicious'),
            'use_featured_image' => __('Use As Logo', 'affilicious'),
            'insert_into_item' => __('Insert Into Shop', 'affilicious'),
            'uploaded_to_this_item' => __('Uploaded To This Shop', 'affilicious'),
            'items_list' => __('Shop', 'affilicious'),
            'items_list_navigation' => __('Shop Navigation', 'affilicious'),
            'filter_items_list' => __('Filter Shops', 'affilicious'),
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
                $new['logo'] = __('Logo', 'affilicious');
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
