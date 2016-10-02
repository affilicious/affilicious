<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Attribute\Domain\Model\AttributeGroupRepositoryInterface;
use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;
use Carbon_Fields\Container as CarbonContainer;
use Carbon_Fields\Field as CarbonField;
use Carbon_Fields\Field\Complex_Field as CarbonComplexField;
use Carbon_Fields\Field\Select_Field as CarbonSelectField;

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
     * @var AttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;

    /**
     * @var ShopRepositoryInterface
     */
    private $shopRepository;

    /**
     * @since 0.6
     * @param DetailGroupRepositoryInterface $detailGroupRepository
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     * @param ShopRepositoryInterface $shopRepository
     */
    public function __construct(
        DetailGroupRepositoryInterface $detailGroupRepository,
        AttributeGroupRepositoryInterface $attributeGroupRepository,
        ShopRepositoryInterface $shopRepository
    )
    {
        $this->detailGroupRepository = $detailGroupRepository;
        $this->attributeGroupRepository = $attributeGroupRepository;
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
        CarbonContainer::make('post_meta', __('Products', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('core')
            ->add_tab(__('General', 'affilicious'), $this->getGeneralFields())
            ->add_tab(__('Variants', 'affilicious'), $this->getVariantsFields())
            ->add_tab(__('Shops', 'affilicious'), $this->getShopsFields())
            ->add_tab(__('Details', 'affilicious'), $this->getDetailsFields())
            ->add_tab(__('Review', 'affilicious'), $this->getReviewFields())
            ->add_tab(__('Relations', 'affilicious'), $this->getRelationsFields());
    }

    /**
     * Get the general fields
     *
     * @since 0.6
     * @return array
     */
    private function getGeneralFields()
    {
        $fields = array(
            CarbonField::make('select', CarbonProductRepository::TYPE, __('Type', 'affilicious'))
                ->add_options(array(
                    'simple' => __('Simple', 'affilicious'),
                    'variants' => __('Variants', 'affilicious'),
                ))
        );

        return $fields;
    }

    /**
     * Get the variants fields
     *
     * @since 0.6
     * @return array
     */
    private function getVariantsFields()
    {
        $fields = array(
            CarbonField::make('complex', CarbonProductRepository::VARIANTS, __('Variants', 'affilicious'))
                ->setup_labels(array(
                    'plural_name' => __('Variants', 'affilicious'),
                    'singular_name' => __('Variant', 'affilicious'),
                ))
                ->add_fields(array(
                    CarbonField::make('text',
                        CarbonProductRepository::VARIANTS_TITLE,
                        __('Title', 'affilicious')
                    )
                    ->set_required(true),
                    $this->getAttributeGroupTabs(
                        CarbonProductRepository::VARIANTS_ATTRIBUTE_GROUPS,
                        __('Attribute Groups', 'affilicious')
                    ),
                    CarbonField::make('image',
                        CarbonProductRepository::VARIANTS_THUMBNAIL,
                        __('Thumbnail', 'affilicious')
                    ),
                    $this->getShopTabs(
                        CarbonProductRepository::VARIANTS_SHOPS,
                        __('Shops', 'affilicious')
                    ),
                ))
        );

        return $fields;
    }

    /**
     * Get the shops fields
     *
     * @since 0.6
     * @return array
     */
    private function getShopsFields()
    {
        $fields = array(
            $this->getShopTabs(
                CarbonProductRepository::SHOPS,
                __('Shops', 'affilicious')
            ),
        );

        return $fields;
    }

    /**
     * Get the details fields
     *
     * @since 0.6
     * @return array
     */
    private function getDetailsFields()
    {
        $fields = array(
            $this->getDetailGroupTabs(
                CarbonProductRepository::DETAIL_GROUPS,
                __('Detail Groups', 'affilicious')
            )
        );

        return $fields;
    }

    /**
     * Get the review fields
     *
     * @since 0.6
     * @return array
     */
    private function getReviewFields()
    {
        $fields = array(
            CarbonField::make('select', CarbonProductRepository::REVIEW_RATING, __('Rating', 'affilicious'))
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
            CarbonField::make('number', CarbonProductRepository::REVIEW_VOTES, __('Votes', 'affilicious'))
                ->set_help_text(__('If you want to hide the votes, just leave it empty.', 'affilicious'))
                ->set_conditional_logic(array(
                    'relation' => 'AND',
                    array(
                        'field' => CarbonProductRepository::REVIEW_RATING,
                        'value' => 'none',
                        'compare' => '!=',
                    )
                )),
        );

        return $fields;
    }

    /**
     * Get the relation fields
     *
     * @since 0.6
     * @return array
     */
    private function getRelationsFields()
    {
        $fields = array(
            CarbonField::make('relationship', CarbonProductRepository::RELATED_PRODUCTS, __('Related Products', 'affilicious'))
                ->allow_duplicates(false)
                ->set_post_type(Product::POST_TYPE),
            CarbonField::make('relationship', CarbonProductRepository::RELATED_ACCESSORIES, __('Related Accessories', 'affilicious'))
                ->allow_duplicates(false)
                ->set_post_type(Product::POST_TYPE),
        );

        return $fields;
    }

    /**
     * Get the shops tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return CarbonComplexField
     */
    private function getShopTabs($name, $label = null)
    {
        $shops = $this->shopRepository->findAll();

        /** @var CarbonComplexField $tabs */
        $tabs = CarbonField::make('complex', $name, $label)
            ->set_layout('tabbed')
            ->setup_labels(array(
                'plural_name' => __('Shops', 'affilicious'),
                'singular_name' => __('Shop', 'affilicious'),
            ));

        foreach ($shops as $shop) {
            $fields = array(
                CarbonField::make('hidden', CarbonProductRepository::SHOPS_ID, __('Shop ID', 'affilicious'))
                    ->set_required(true)
                    ->set_value($shop->getId()->getValue()),
                CarbonField::make('text', CarbonProductRepository::SHOPS_AFFILIATE_ID, __('Affiliate ID', 'affilicious'))
                    ->set_required(true)
                    ->set_help_text(__('Unique product ID of the shop like Amazon ASIN, Affilinet ID, ebay ID, etc.', 'affilicious')),
                CarbonField::make('text', CarbonProductRepository::SHOPS_AFFILIATE_LINK, __('Affiliate Link', 'affilicious'))
                    ->set_required(true),
                CarbonField::make('number', CarbonProductRepository::SHOPS_PRICE, __('Price', 'affilicious')),
                CarbonField::make('number', CarbonProductRepository::SHOPS_OLD_PRICE, __('Old Price', 'affilicious')),
                CarbonField::make('select', CarbonProductRepository::SHOPS_CURRENCY, __('Currency', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        'euro' => __('Euro', 'affilicious'),
                        'us-dollar' => __('US-Dollar', 'affilicious'),
                    )),
            );

            $tabs->add_fields($shop->getTitle(), $fields);
        }

        return $tabs;
    }

    /**
     * Get the attribute groups tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return CarbonComplexField
     */
    private function getAttributeGroupTabs($name, $label = null)
    {
        $attributeGroups = $this->attributeGroupRepository->findAll();

        /** @var CarbonComplexField $tabs */
        $tabs = CarbonField::make('complex', $name, $label)
            ->set_layout('tabbed')
            ->setup_labels(array(
                'plural_name' => __('Attribute Groups', 'affilicious'),
                'singular_name' => __('Attribute Group', 'affilicious'),
            ));

        foreach ($attributeGroups as $attributeGroup) {
            $title = $attributeGroup->getTitle()->getValue();
            $key = DatabaseHelper::convertTextToKey($title);

            if (empty($title) || empty($key)) {
                continue;
            }

            $attributes = $attributeGroup->getAttributes();
            $fields = array();
            foreach ($attributes as $attribute) {
                $value = $attribute->getValue();
                $values = explode(';', $value);

                $temp = array();
                foreach ($values as $value) {
                    $key = DatabaseHelper::convertTextToKey($value);
                    $temp[$key] = $value;
                }

                /** @var CarbonSelectField $field */
                $field = CarbonField::make(
                    'select',
                    $attribute->getKey()->getValue(),
                    $attribute->getTitle()->getValue()
                )->add_options($temp);

                if ($attribute->hasHelpText()) {
                    $field->help_text($attribute->getHelpText()->getValue());
                }

                $fields[] = $field;
            }

            $fieldId = CarbonField::make(
                'hidden',
                CarbonProductRepository::VARIANTS_ATTRIBUTE_GROUPS_ID
            )->set_value($attributeGroup->getId()->getValue());

            $fields = array_merge(array(
                CarbonProductRepository::VARIANTS_ATTRIBUTE_GROUPS_ID => $fieldId,
            ), $fields);

            $tabs->add_fields($key, $title, $fields);
        }

        return $tabs;
    }

    /**
     * Get the detail groups tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return CarbonComplexField
     */
    private function getDetailGroupTabs($name, $label = null)
    {
        $detailGroups = $this->detailGroupRepository->findAll();

        /** @var CarbonComplexField $tabs */
        $tabs = CarbonField::make('complex', $name, $label)
            ->set_layout('tabbed')
            ->setup_labels(array(
                'plural_name' => __('Detail Groups', 'affilicious'),
                'singular_name' => __('Detail Group', 'affilicious'),
            ));

        foreach ($detailGroups as $detailGroup) {
            $title = $detailGroup->getTitle()->getValue();
            $key = DatabaseHelper::convertTextToKey($title);

            if (empty($title) || empty($key)) {
                continue;
            }

            $fields = array();
            $details = $detailGroup->getDetails();
            foreach ($details as $detail) {
                $fieldName = sprintf('%s %s', $detail->getTitle(), $detail->getUnit());
                $fieldName = trim($fieldName);

                $field = CarbonField::make(
                    $detail->getType(),
                    $detail->getKey(),
                    $fieldName
                );

                if ($detail->hasHelpText()) {
                    $field->help_text($detail->getHelpText());
                }

                $fields[] = $field;
            }

            $carbonDetailGroupId = CarbonField::make(
                'hidden',
                CarbonProductRepository::DETAIL_GROUPS_ID
            )->set_value($detailGroup->getId()->getValue());

            $fields = array_merge(array(
                CarbonProductRepository::DETAIL_GROUPS_ID => $carbonDetailGroupId,
            ), $fields);

            $tabs->add_fields($key, $title, $fields);
        }

        return $tabs;
    }
}
