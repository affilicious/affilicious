<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupRepositoryInterface;
use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Common\Application\Setup\SetupInterface;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupRepositoryInterface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Shop\Domain\Model\ShopTemplateRepositoryInterface;
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
     * @var ShopTemplateRepositoryInterface
     */
    protected $shopTemplateRepository;

    /**
     * @var DetailTemplateGroupRepositoryInterface
     */
    protected $detailTemplateGroupRepository;

    /**
     * @var AttributeTemplateGroupRepositoryInterface
     */
    protected $attributeGroupRepository;

    /**
     * @since 0.6
     * @param DetailTemplateGroupRepositoryInterface $detailTemplateGroupRepository
     * @param AttributeTemplateGroupRepositoryInterface $attributeTemplateGroupRepository
     * @param ShopTemplateRepositoryInterface $shopTemplateRepository
     */
    public function __construct(
        ShopTemplateRepositoryInterface $shopTemplateRepository,
        AttributeTemplateGroupRepositoryInterface $attributeTemplateGroupRepository,
        DetailTemplateGroupRepositoryInterface $detailTemplateGroupRepository
    )
    {
        $this->shopTemplateRepository = $shopTemplateRepository;
        $this->attributeGroupRepository = $attributeTemplateGroupRepository;
        $this->detailTemplateGroupRepository = $detailTemplateGroupRepository;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $singular = __('Product', 'affilicious');
        $plural = __('Products', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $singular,
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Product', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Product', 'affilicious'), $singular),
            'all_items'             => __('Products', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Product', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Product', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Product', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Product', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Product', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Product', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Product', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded To This %s', 'Product', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Product', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Product', 'affilicious'), $plural),
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
                        CarbonProductRepository::VARIANT_TITLE,
                        __('Title', 'affilicious')
                    )
                    ->set_required(true),
                    $this->getAttributeGroupTabs(
                        CarbonProductRepository::VARIANT_ATTRIBUTE_GROUPS,
                        __('AttributeTemplate Groups', 'affilicious')
                    ),
                    CarbonField::make('image',
                        CarbonProductRepository::VARIANT_THUMBNAIL,
                        __('Thumbnail', 'affilicious')
                    ),
                    $this->getShopTabs(
                        CarbonProductRepository::VARIANT_SHOPS,
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
                __('DetailTemplate Groups', 'affilicious')
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
        $shops = $this->shopTemplateRepository->findAll();

        /** @var CarbonComplexField $tabs */
        $tabs = CarbonField::make('complex', $name, $label)
            ->set_layout('tabbed')
            ->setup_labels(array(
                'plural_name' => __('Shops', 'affilicious'),
                'singular_name' => __('ShopTemplate', 'affilicious'),
            ));

        foreach ($shops as $shop) {
            $fields = array(
                CarbonField::make('hidden', CarbonProductRepository::SHOP_ID, __('ShopTemplate ID', 'affilicious'))
                    ->set_required(true)
                    ->set_value($shop->getId()->getValue()),
                CarbonField::make('text', CarbonProductRepository::SHOP_AFFILIATE_ID, __('Affiliate ID', 'affilicious'))
                    ->set_required(true)
                    ->set_help_text(__('Unique product ID of the shop like Amazon ASIN, Affilinet ID, ebay ID, etc.', 'affilicious')),
                CarbonField::make('text', CarbonProductRepository::SHOP_AFFILIATE_LINK, __('Affiliate Link', 'affilicious'))
                    ->set_required(true),
                CarbonField::make('number', CarbonProductRepository::SHOP_PRICE, __('Price', 'affilicious')),
                CarbonField::make('number', CarbonProductRepository::SHOP_OLD_PRICE, __('Old Price', 'affilicious')),
                CarbonField::make('select', CarbonProductRepository::SHOP_CURRENCY, __('Currency', 'affilicious'))
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
                'plural_name' => __('AttributeTemplate Groups', 'affilicious'),
                'singular_name' => __('AttributeTemplate Group', 'affilicious'),
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
                CarbonProductRepository::VARIANT_ATTRIBUTE_GROUPS_ID
            )->set_value($attributeGroup->getId()->getValue());

            $fields = array_merge(array(
                CarbonProductRepository::VARIANT_ATTRIBUTE_GROUPS_ID => $fieldId,
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
        $detailGroups = $this->detailTemplateGroupRepository->findAll();

        /** @var CarbonComplexField $tabs */
        $tabs = CarbonField::make('complex', $name, $label)
            ->set_layout('tabbed')
            ->setup_labels(array(
                'plural_name' => __('DetailTemplate Groups', 'affilicious'),
                'singular_name' => __('DetailTemplate Group', 'affilicious'),
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
