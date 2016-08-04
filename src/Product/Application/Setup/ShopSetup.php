<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;
use Affilicious\ProductsPlugin\Product\Domain\Model\ShopRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Wordpress\WordpressShopRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ShopSetup implements SetupInterface
{
    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * Hook into the required Wordpress actions
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'), 1);
        add_action('init', array($this, 'render'), 2);
        add_filter('manage_shops_posts_columns', array($this, 'columnsHead'), 9, 2);
        add_action('manage_shops_posts_custom_column', array($this, 'columnsContent'), 10, 2);

        $this->shopRepository = new WordpressShopRepository();
    }

    /**
     * @inheritdoc
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
            'show_in_menu' => 'edit.php?post_type=product',
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $query = new \WP_Query(array(
            'post_type' => Shop::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => array(
                'post_title' => 'ASC',
            ),
        ));

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_SHOPS, __('Shops', 'affiliciousproducts'))
            ->set_layout('tabbed');

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = $this->shopRepository->findById($query->post->ID);

                $tabs->add_fields($shop->getTitle(), array(
                    CarbonField::make('hidden', 'shop_id', __('Shop ID', 'affiliciousproducts'))
                        ->set_required(true)
                        ->set_value($shop->getId()),
                    CarbonField::make('text', 'price', __('Price', 'affiliciousproducts'))
                        ->set_required(true),
                    CarbonField::make('text', 'old_price', __('Old Price', 'affiliciousproducts')),
                    CarbonField::make('select', 'currency', __('Currency', 'affiliciousproducts'))
                        ->set_required(true)
                        ->add_options(array(
                            'Euro' => __('Euro', 'affiliciousproducts'),
                            'US-Dollar' => __('US-Dollar', 'affiliciousproducts'),
                        )),
                    CarbonField::make('text', 'affiliate_id', __('Affiliate ID', 'affiliciousproducts'))
                        ->help_text(__('Unique product ID (e.g. Amazon ASIN or Affilinet ID)', 'affiliciousproducts')),
                    CarbonField::make('text', 'affiliate_link', __('Affiliate Link', 'affiliciousproducts')),
                ));
            }

            wp_reset_postdata();
        }

        CarbonContainer::make('post_meta', __('Price Comparison', 'affiliciousproducts'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array(
                CarbonField::make('text', CarbonProductRepository::PRODUCT_EAN, __('EAN', 'affiliciousproducts'))
                    ->help_text(__('Unique ID for the price comparison', 'affiliciousproducts')),
                $tabs
            ));
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
