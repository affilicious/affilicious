<?php
namespace Affilicious\ProductsPlugin\Product\Application\Setup;

use Affilicious\ProductsPlugin\Product\Domain\Model\Field;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroup;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
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
     * @param DetailGroupRepositoryInterface $detailGroupRepository
     */
    public function __construct(DetailGroupRepositoryInterface $detailGroupRepository)
    {
        $this->detailGroupRepository = $detailGroupRepository;
    }

    /**
     * @inheritdoc
     */
    public function init()
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

        register_post_type(Product::POST_TYPE, $args);

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
            'rewrite' => array('slug' => Product::SLUG),
            'public' => true,
        ));
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if(!$query->have_posts()) {
            return;
        }

        $tabs = CarbonField::make('complex', CarbonProductRepository::PRODUCT_DETAIL_GROUPS, __('Detail Groups', 'affiliciousproducts'))
            ->set_layout('tabbed');

        while ($query->have_posts()) {
            $query->the_post();

            $detailGroup = $this->detailGroupRepository->findById($query->post->ID);
            $title = $detailGroup->getTitle();
            $name = $detailGroup->getName();

            if (empty($title) || empty($name)) {
                continue;
            }

            $carbonFields = array_map(function($field) {
                $carbonField = CarbonField::make(
                    $field[DetailGroup::DETAIL_TYPE],
                    $field[DetailGroup::DETAIL_KEY],
                    $field[DetailGroup::DETAIL_LABEL]
                );

                if (!empty($field[DetailGroup::DETAIL_DEFAULT_VALUE])) {
                    $carbonField->set_default_value($field[DetailGroup::DETAIL_DEFAULT_VALUE]);
                }

                if (!empty($field[DetailGroup::DETAIL_HELP_TEXT])) {
                    $carbonField->help_text($field[DetailGroup::DETAIL_HELP_TEXT]);
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

        CarbonContainer::make('post_meta', __('Details', 'affiliciousproducts'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array($tabs));

        $this->renderRelations();
    }

    public function renderRelations()
    {
        CarbonContainer::make('post_meta', __('Relations', 'affiliciousproducts'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_tab(__('Products'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_PRODUCTS, __('Related Products', 'affiliciousproducts'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Accessories'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_ACCESSORIES, __('Related Accessories', 'affiliciousproducts'))
                    ->allow_duplicates(false)
                    ->set_post_type(Product::POST_TYPE),
            ))
            ->add_tab(__('Posts'), array(
                CarbonField::make('relationship', CarbonProductRepository::PRODUCT_RELATED_POSTS, __('Related Posts', 'affiliciousproducts'))
                    ->allow_duplicates(false)
                    ->set_post_type('post'),
            ));
    }
}
