<?php
namespace Affilicious\ProductsPlugin\Shop;

use Affilicious\ProductsPlugin\Product\Product;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ShopSetup
{
    /**
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 2);
        add_action('init', array($this, 'render'), 3);
        add_filter('manage_shops_posts_columns', array($this, 'columnsHead'), 9, 2);
        add_action('manage_shops_posts_custom_column', array($this, 'columnsContent'), 10, 2);
    }

    /**
     * Initialize the shop type
     */
    public function init()
    {
        $labels = array(
            'name' => _x('Shops', 'affiliciousproducts'),
            'singular_name' => _x('Shop', 'affiliciousproducts'),
            'menu_name' => __('Shop', 'affiliciousproducts'),
            'name_admin_bar' => __('Shop', 'affiliciousproducts'),
            'archives' => __('Shop Archives', 'affiliciousproducts'),
            'parent_item_colon' => __('Parent Shop:', 'affiliciousproducts'),
            'all_items' => __('Shops', 'affiliciousproducts'),
            'add_new_item' => __('Add New Shop', 'affiliciousproducts'),
            'add_new' => __('Add New', 'affiliciousproducts'),
            'new_item' => __('New Shop', 'affiliciousproducts'),
            'edit_item' => __('Edit Shop', 'affiliciousproducts'),
            'update_item' => __('Update Shop', 'affiliciousproducts'),
            'view_item' => __('View Shop', 'affiliciousproducts'),
            'search_items' => __('Search Shop', 'affiliciousproducts'),
            'not_found' => __('Not Found', 'affiliciousproducts'),
            'not_found_in_trash' => __('Not Found In Trash', 'affiliciousproducts'),
            'featured_image' => __('Logo', 'affiliciousproducts'),
            'set_featured_image' => __('Set Logo', 'affiliciousproducts'),
            'remove_featured_image' => __('Remove Logo', 'affiliciousproducts'),
            'use_featured_image' => __('Use As Logo', 'affiliciousproducts'),
            'insert_into_item' => __('Insert Into Shop', 'affiliciousproducts'),
            'uploaded_to_this_item' => __('Uploaded To This Shop', 'affiliciousproducts'),
            'items_list' => __('Shop', 'affiliciousproducts'),
            'items_list_navigation' => __('Shop Navigation', 'affiliciousproducts'),
            'filter_items_list' => __('Filter Shops', 'affiliciousproducts'),
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
            'show_in_menu' => true,
        ));
    }

    /**
     * Render a single shop type
     */
    public function render()
    {
        /*CarbonContainer::make('post_meta', __('Shops', 'affiliciousproducts'))
            ->show_on_post_type(Product::POST_TYPE)
            ->add_fields(array(
                CarbonField::make('complex', 'affilicious_shops')
                    ->set_layout('tabbed')
                    ->add_fields('amazon', array(
                        CarbonField::make('text', 'price', __('Price', 'affiliciousproducts')),
                        CarbonField::make('text', 'old_price', __('Old Price', 'affiliciousproducts')),
                        CarbonField::make('select', 'currency', __('Currency', 'affiliciousproducts'))
                            ->add_options(array(
                                'Euro' => __('Euro', 'affiliciousproducts'),
                                'US-Dollar' => __('US-Dollar', 'affiliciousproducts'),
                            ))
                    ))
                    ->add_fields('affilinet', array(
                        CarbonField::make('text', 'price', __('Price', 'affiliciousproducts')),
                        CarbonField::make('text', 'old_price', __('Old Price', 'affiliciousproducts')),
                        CarbonField::make('select', 'currency', __('Currency', 'affiliciousproducts'))
                            ->add_options(array(
                                'Euro' => __('Euro', 'affiliciousproducts'),
                                'US-Dollar' => __('US-Dollar', 'affiliciousproducts'),
                            ))
                    ))
            ));*/
    }

    /**
     * Add a column header for the logo
     * @param array $defaults
     * @return array
     */
    public function columnsHead($defaults)
    {
        $new = array();
        foreach($defaults as $key => $title) {
            // Put the logo column before the date column
            if ($key=='date') {
                $new['logo'] = __('Logo', 'affiliciousproducts');
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
