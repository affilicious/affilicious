<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Product\Domain\Helper\DetailGroupHelper;
use Affilicious\Product\Domain\Model\DetailGroup;
use Affilicious\Product\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\Shop;
use Affilicious\Product\Domain\Model\ShopRepositoryInterface;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

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
     * @since 0.2
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
            'name' => __('Products', 'affilicious'),
            'singular_name' => __('Product', 'affilicious'),
            'menu_name' => __('Products', 'affilicious'),
            'name_admin_bar' => __('Product', 'affilicious'),
            'archives' => __('Item Archives', 'affilicious'),
            'parent_item_colon' => __('Parent Item:', 'affilicious'),
            'all_items' => __('All Products', 'affilicious'),
            'add_new_item' => __('Add New Product', 'affilicious'),
            'add_new' => __('Add New', 'affilicious'),
            'new_item' => __('New Product', 'affilicious'),
            'edit_item' => __('Edit Product', 'affilicious'),
            'update_item' => __('Update Product', 'affilicious'),
            'view_item' => __('View Product', 'affilicious'),
            'search_items' => __('Search Product', 'affilicious'),
            'not_found' => __('Not found', 'affilicious'),
            'not_found_in_trash' => __('Not found in Trash', 'affilicious'),
            'featured_image' => __('Featured Image', 'affilicious'),
            'set_featured_image' => __('Set featured image', 'affilicious'),
            'remove_featured_image' => __('Remove featured image', 'affilicious'),
            'use_featured_image' => __('Use as featured image', 'affilicious'),
            'insert_into_item' => __('Insert into item', 'affilicious'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'affilicious'),
            'items_list' => __('Products list', 'affilicious'),
            'items_list_navigation' => __('Products list navigation', 'affilicious'),
            'filter_items_list' => __('Filter items list', 'affilicious'),
        );

        $args = array(
            'label' => __('Product', 'affilicious'),
            'description' => __('Product Type Description', 'affilicious'),
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
            'name' => __('Product Categories', 'affilicious'),
            'singular_name' => __('Product Category', 'affilicious'),
            'search_items' => __('Search Product Categories', 'affilicious'),
            'all_items' => __('All Product Categories', 'affilicious'),
            'parent_item' => __('Parent Product Category', 'affilicious'),
            'parent_item_colon' => __('Parent Product Category:', 'affilicious'),
            'edit_item' => __('Edit Product Category', 'affilicious'),
            'update_item' => __('Update Product Category', 'affilicious'),
            'add_new_item' => __('Add New Product Category', 'affilicious'),
            'new_item_name' => __('New Product Category Name', 'affilicious'),
            'menu_name' => __('Categories', 'affilicious'),
        );

        register_taxonomy(Product::TAXONOMY, Product::POST_TYPE, array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
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
     *
     * @since 0.3
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

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_SHOPS, __('Shops', 'affilicious'))
            ->set_layout('tabbed');

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = $this->shopRepository->findById($query->post->ID);

                $tabs->add_fields($shop->getTitle(), array(
                    CarbonField::make('hidden', 'shop_id', __('Shop ID', 'affilicious'))
                        ->set_required(true)
                        ->set_value($shop->getId()),
                    CarbonField::make('number', 'price', __('Price', 'affilicious'))
                        ->set_required(true),
                    CarbonField::make('number', 'old_price', __('Old Price', 'affilicious')),
                    CarbonField::make('select', 'currency', __('Currency', 'affilicious'))
                        ->set_required(true)
                        ->add_options(array(
                            'euro' => __('Euro', 'affilicious'),
                            'us-dollar' => __('US-Dollar', 'affilicious'),
                        )),
                    CarbonField::make('text', 'affiliate_link', __('Affiliate Link', 'affilicious')),
                ));
            }

            wp_reset_postdata();
        }

        $carbonContainer = CarbonContainer::make('post_meta', __('Price Comparison', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array(
                CarbonField::make('text', CarbonProductRepository::PRODUCT_EAN, __('EAN', 'affilicious'))
                    ->help_text(__('Unique ID for the price comparison', 'affilicious')),
                $tabs
            ));

        apply_filters('affilicious_product_render_price_comparison', $carbonContainer);
    }

    /**
     * Render the details
     *
     * @since 0.3
     */
    private function renderDetails()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if (!$query->have_posts()) {
            return;
        }

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_DETAIL_GROUPS, __('Detail Groups', 'affilicious'))
            ->set_layout('tabbed');

        while ($query->have_posts()) {
            $query->the_post();

            $detailGroup = $this->detailGroupRepository->findById($query->post->ID);
            $title = $detailGroup->getTitle();
            $name = DetailGroupHelper::convertNameToKey($title);

            if (empty($title) || empty($name)) {
                continue;
            }

            $carbonFields = array_map(function ($detail) {
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

            $carbonDetailGroupId = CarbonField::make('hidden', 'detail_group_id')
                ->set_value($detailGroup->getId());

            $carbonFields = array_merge(array(
                'detail_group_id' => $carbonDetailGroupId,
            ), $carbonFields);

            $tabs->add_fields($name, $title, $carbonFields);
        }

        $carbonContainer = CarbonContainer::make('post_meta', __('Details', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array($tabs));

        apply_filters('affilicious_product_render_details', $carbonContainer);
    }

    /**
     * Render the relation fields
     *
     * @since 0.3
     */
    private function renderRelations()
    {
        $carbonContainer = CarbonContainer::make('post_meta', __('Relations', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('low')
            ->add_tab(__('Products', 'affilicious'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_PRODUCTS, __('Related Products', 'affilicious'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Accessories', 'affilicious'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_ACCESSORIES, __('Related Accessories', 'affilicious'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Posts', 'affilicious'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_POSTS, __('Related Posts', 'affilicious'))
                    ->allow_duplicates(false)
                    ->set_post_type('post'),
            ));

        apply_filters('affilicious_product_render_relations', $carbonContainer);
    }

    /**
     * Render the sidebar
     *
     * @since 0.3
     */
    private function renderSidebars()
    {

    }
}
