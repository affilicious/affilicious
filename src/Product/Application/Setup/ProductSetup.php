<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\Field;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroup;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\Shop;
use Affilicious\ProductsPlugin\Product\Domain\Model\ShopRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ProductSetup implements SetupInterface
{
    /**
     * @var DetailGroupRepositoryInterface
     */
    private $detailGroupRepository;

    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * @param DetailGroupRepositoryInterface $detailGroupRepository
     * @param ShopRepositoryInterface $shopRepository
     */
    public function __construct(
        DetailGroupRepositoryInterface $detailGroupRepository,
        ShopRepositoryInterface $shopRepository
    )
    {
        $this->detailGroupRepository = $detailGroupRepository;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $labels = array(
            'name' => __('Products', 'affilicious-products'),
            'singular_name' => __('Product', 'affilicious-products'),
            'menu_name' => __('Products', 'affilicious-products'),
            'name_admin_bar' => __('Product', 'affilicious-products'),
            'archives' => __('Item Archives', 'affilicious-products'),
            'parent_item_colon' => __('Parent Item:', 'affilicious-products'),
            'all_items' => __('All Products', 'affilicious-products'),
            'add_new_item' => __('Add New Product', 'affilicious-products'),
            'add_new' => __('Add New', 'affilicious-products'),
            'new_item' => __('New Product', 'affilicious-products'),
            'edit_item' => __('Edit Product', 'affilicious-products'),
            'update_item' => __('Update Product', 'affilicious-products'),
            'view_item' => __('View Product', 'affilicious-products'),
            'search_items' => __('Search Product', 'affilicious-products'),
            'not_found' => __('Not found', 'affilicious-products'),
            'not_found_in_trash' => __('Not found in Trash', 'affilicious-products'),
            'featured_image' => __('Featured Image', 'affilicious-products'),
            'set_featured_image' => __('Set featured image', 'affilicious-products'),
            'remove_featured_image' => __('Remove featured image', 'affilicious-products'),
            'use_featured_image' => __('Use as featured image', 'affilicious-products'),
            'insert_into_item' => __('Insert into item', 'affilicious-products'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'affilicious-products'),
            'items_list' => __('Products list', 'affilicious-products'),
            'items_list_navigation' => __('Products list navigation', 'affilicious-products'),
            'filter_items_list' => __('Filter items list', 'affilicious-products'),
        );

        $args = array(
            'label' => __('Product', 'affilicious-products'),
            'description' => __('Product Type Description', 'affilicious-products'),
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

        register_post_type(Product::POST_TYPE, $args);

        $labels = array(
            'name' => __('Categories', 'affilicious-products'),
            'singular_name' => __('Category', 'affilicious-products'),
            'search_items' => __('Search categories', 'affilicious-products'),
            'all_items' => __('All categories', 'affilicious-products'),
            'parent_item' => __('Parent category', 'affilicious-products'),
            'parent_item_colon' => __('Parent category:', 'affilicious-products'),
            'edit_item' => __('Edit category', 'affilicious-products'),
            'update_item' => __('Update category', 'affilicious-products'),
            'add_new_item' => __('Add New category', 'affilicious-products'),
            'new_item_name' => __('New category name', 'affilicious-products'),
            'menu_name' => __('Categories', 'affilicious-products'),
        );

        register_taxonomy(Product::TAXONOMY, Product::POST_TYPE, array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => Product::SLUG),
            'public' => true,
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->renderPriceComparison();
        $this->renderDetails();
        $this->renderRelations();
        $this->renderSidebars();
    }

    /**
     * Render the price comparison
     */
    private function renderPriceComparison()
    {
        $query = new \WP_Query(array(
            'post_type' => Shop::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => array(
                'post_title' => 'ASC',
            ),
        ));

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_SHOPS, __('Shops', 'affilicious-products'))
            ->set_layout('tabbed');

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = $this->shopRepository->findById($query->post->ID);

                $tabs->add_fields($shop->getTitle(), array(
                    CarbonField::make('hidden', 'shop_id', __('Shop ID', 'affilicious-products'))
                        ->set_required(true)
                        ->set_value($shop->getId()),
                    CarbonField::make('number', 'price', __('Price', 'affilicious-products'))
                        ->set_required(true),
                    CarbonField::make('number', 'old_price', __('Old Price', 'affilicious-products')),
                    CarbonField::make('select', 'currency', __('Currency', 'affilicious-products'))
                        ->set_required(true)
                        ->add_options(array(
                            'euro' => __('Euro', 'affilicious-products'),
                            'us-dollar' => __('US-Dollar', 'affilicious-products'),
                        )),
                    CarbonField::make('text', 'affiliate_link', __('Affiliate Link', 'affilicious-products')),
                ));
            }

            wp_reset_postdata();
        }

        CarbonContainer::make('post_meta', __('Price Comparison', 'affilicious-products'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array(
                CarbonField::make('text', CarbonProductRepository::PRODUCT_EAN, __('EAN', 'affilicious-products'))
                    ->help_text(__('Unique ID for the price comparison', 'affilicious-products')),
                $tabs
            ));
    }

    /**
     * Render the details
     */
    private function renderDetails()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if(!$query->have_posts()) {
            return;
        }

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_DETAIL_GROUPS, __('Detail Groups', 'affilicious-products'))
            ->set_layout('tabbed');

        while ($query->have_posts()) {
            $query->the_post();

            $detailGroup = $this->detailGroupRepository->findById($query->post->ID);
            $title = $detailGroup->getTitle();
            $name = $detailGroup->getName();

            if (empty($title) || empty($name)) {
                continue;
            }

            $carbonFields = array_map(function($detail) {
                $carbonField = CarbonField::make(
                    $detail[DetailGroup::DETAIL_TYPE],
                    $detail[DetailGroup::DETAIL_KEY],
                    $detail[DetailGroup::DETAIL_NAME]
                );

                if (!empty($detail[DetailGroup::DETAIL_DEFAULT_VALUE])) {
                    $carbonField->set_default_value($detail[DetailGroup::DETAIL_DEFAULT_VALUE]);
                }

                if (!empty($detail[DetailGroup::DETAIL_HELP_TEXT])) {
                    $carbonField->help_text($detail[DetailGroup::DETAIL_HELP_TEXT]);
                }

                return $carbonField;
            }, $detailGroup->getDetails());

            if (!empty($carbonFields)) {
                $carbonDetailGroupId = CarbonField::make('hidden', 'detail_group_id')
                    ->set_value($detailGroup->getId());

                $carbonFields = array_merge(array(
                    'detail_group_id' => $carbonDetailGroupId,
                ), $carbonFields);

                $tabs->add_fields($name, $title, $carbonFields);
            }
        }

        CarbonContainer::make('post_meta', __('Details', 'affilicious-products'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array($tabs));
    }

    /**
     * Render the relation fields
     */
    private function renderRelations()
    {
        CarbonContainer::make('post_meta', __('Relations', 'affilicious-products'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('low')
            ->add_tab(__('Products', 'affilicious-products'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_PRODUCTS, __('Related Products', 'affilicious-products'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Accessories', 'affilicious-products'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_ACCESSORIES, __('Related Accessories', 'affilicious-products'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Posts', 'affilicious-products'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_POSTS, __('Related Posts', 'affilicious-products'))
                    ->allow_duplicates(false)
                    ->set_post_type('post'),
            ));
    }

    /**
     * Render the sidebar
     */
    private function renderSidebars()
    {
        CarbonContainer::make('post_meta', __('Post Sidebar', 'affilicious-products'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('low')
            ->add_fields(array(
                CarbonField::make("sidebar", "crb_custom_sidebar", "Select a Sidebar")
            ));
    }
}
