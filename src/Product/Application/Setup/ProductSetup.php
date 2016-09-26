<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Detail\Domain\Model\DetailGroupId;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

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

	    $slug = carbon_get_theme_option('affilicious_settings_product_general_slug');
	    if(empty($slug)) {
	    	$slug = Product::SLUG;
	    }

        $args = array(
            'label' => __('Product', 'affilicious'),
            'description' => __('Product Type Description', 'affilicious'),
            'labels' => $labels,
            'menu_icon' => 'dashicons-products',
            'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions'),
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
	        'rewrite' => array('slug' => $slug)
        );

        register_post_type(Product::POST_TYPE, $args);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->renderPriceComparison();
        $this->renderDetails();
	    $this->renderReview();
        $this->renderRelations();
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

                $shopId = $query->post->ID;
                $title = $query->post->post_title;

                $tabs->add_fields($title, array(
                    CarbonField::make('hidden', 'shop_id', __('Shop ID', 'affilicious'))
                        ->set_required(true)
                        ->set_value($shopId),
                    CarbonField::make('number', 'price', __('Price', 'affilicious')),
                    CarbonField::make('number', 'old_price', __('Old Price', 'affilicious')),
                    CarbonField::make('select', 'currency', __('Currency', 'affilicious'))
                        ->set_required(true)
                        ->add_options(array(
                            'euro' => __('Euro', 'affilicious'),
                            'us-dollar' => __('US-Dollar', 'affilicious'),
                        )),
                    CarbonField::make('text', 'affiliate_id', __('Affiliate ID', 'affilicious'))
                        ->set_help_text(__('Unique product ID of the shop like Amazon ASIN, Affilinet ID, ebay ID, etc.', 'affilicious')),
                    CarbonField::make('text', 'affiliate_link', __('Affiliate Link', 'affilicious')),
                ));
            }

            wp_reset_postdata();
        }

        $carbonContainer = CarbonContainer::make('post_meta', __('Price Comparison', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('default')
            ->add_fields(array($tabs));

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

            $detailGroup = $this->detailGroupRepository->findById(new DetailGroupId($query->post->ID));
            $title = $detailGroup->getTitle()->getValue();
            $name = DatabaseHelper::convertTextToKey($title);

            if (empty($title) || empty($name)) {
                continue;
            }

            $carbonFields = array();
            foreach ($detailGroup->getDetails() as $detail) {
                $fieldName = sprintf('%s %s', $detail->getName(), $detail->getUnit());
                $fieldName = trim($fieldName);

                $carbonField = CarbonField::make(
                    $detail->getType(),
                    $detail->getKey(),
                    $fieldName
                );

                if ($detail->hasHelpText()) {
                    $carbonField->help_text($detail->getHelpText());
                }

                $carbonFields[] = $carbonField;
            }

            $carbonDetailGroupId = CarbonField::make('hidden', 'detail_group_id')
                ->set_value($detailGroup->getId()->getValue());

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
	 * Render the rating
	 *
	 * @since 0.6
	 */
    private function renderReview()
    {
	    $carbonContainer = CarbonContainer::make('post_meta', __('Review', 'affilicious'))
          ->show_on_post_type(Product::POST_TYPE)
          ->set_priority('default')
          ->add_fields(array(
              CarbonField::make('select', CarbonProductRepository::PRODUCT_REVIEW_RATING, __('Rating', 'affilicious'))
                  ->add_options(array(
                      'none' => sprintf(__('None', 'affilicious'), 0),
                      '0' => sprintf(__('%s stars', 'affilicious'), 0),
                      '0.5' => sprintf(__('%s stars', 'affilicious'), 0.5),
                      '1' => sprintf(__('%s star', 'affilicious'), 1),
                      '1.5' => sprintf(__('%s stars', 'affilicious'), 1.5),
                      '2' => sprintf(__('%s stars', 'affilicious'), 2),
                      '2.5' => sprintf(__('%s stars', 'affilicious'), 2.5),
                      '3' => sprintf(__('%s stars', 'affilicious'), 3),
                      '3.5' => sprintf(__('%s stars', 'affilicious'), 3.5),
                      '4' => sprintf(__('%s stars', 'affilicious'), 4),
                      '4.5' => sprintf(__('%s stars', 'affilicious'), 4.5),
                      '5' => sprintf(__('%s stars', 'affilicious'), 5),
                  )),
	          CarbonField::make('number', CarbonProductRepository::PRODUCT_REVIEW_VOTES, __('Votes', 'affilicious'))
                  ->set_help_text(__('If you want to hide the votes, just leave it empty.', 'affilicious'))
                  ->set_conditional_logic(array(
                      'relation' => 'AND',
                      array(
                          'field' => CarbonProductRepository::PRODUCT_REVIEW_RATING,
                          'value' => 'none',
                          'compare' => '!=',
                      )
                  )),
          ));

	    apply_filters('affilicious_product_render_rating', $carbonContainer);
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
            ));

        apply_filters('affilicious_product_render_relations', $carbonContainer);
    }
}
