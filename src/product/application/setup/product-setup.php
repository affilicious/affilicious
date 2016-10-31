<?php
namespace Affilicious\Product\Application\Setup;

use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Repository_Interface;
use Affilicious\Common\Application\Setup\Setup_Interface;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Repository_Interface;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Infrastructure\Repository\Carbon\Carbon_Product_Repository;
use Affilicious\Shop\Domain\Model\Shop_Template_Repository_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;
use Carbon_Fields\Field\Complex_Field as Carbon_Complex_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Setup implements Setup_Interface
{
    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_template_repository;

    /**
     * @var Detail_Template_Group_Repository_Interface
     */
    protected $detail_template_group_repository;

    /**
     * @var Attribute_Template_Group_Repository_Interface
     */
    protected $attribute_template_group_repository;

    /**
     * @var int
     */
    protected $post_id;

    /**
     * @since 0.6
     * @param Detail_Template_Group_Repository_Interface $detail_template_group_repository
     * @param Attribute_Template_Group_Repository_Interface $attribute_template_group_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     */
    public function __construct(
        Shop_Template_Repository_Interface $shop_template_repository,
        Attribute_Template_Group_Repository_Interface $attribute_template_group_repository,
        Detail_Template_Group_Repository_Interface $detail_template_group_repository
    )
    {
        $this->shop_template_repository = $shop_template_repository;
        $this->attribute_template_group_repository = $attribute_template_group_repository;
        $this->detail_template_group_repository = $detail_template_group_repository;
        $this->post_id = isset($_GET['post']) ? $_GET['post'] : null;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        do_action('affilicious_product_before_init');

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
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Product', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Product', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Product', 'affilicious'), $plural),
        );

	    $slug = carbon_get_theme_option('affilicious_options_product_container_general_tab_slug_field');
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

        do_action('affilicious_product_after_init');
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        do_action('affilicious_product_before_render');

        $carbon_container = Carbon_Container::make('post_meta', __('Affilicious Product', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('core')
            ->add_tab(__('General', 'affilicious'), $this->get_general_fields())
            ->add_tab(__('Variants', 'affilicious'), $this->get_variants_fields())
            ->add_tab(__('Shops', 'affilicious'), $this->get_shops_fields())
            ->add_tab(__('Details', 'affilicious'), $this->get_details_fields())
            ->add_tab(__('Review', 'affilicious'), $this->get_review_fields())
            ->add_tab(__('Relations', 'affilicious'), $this->get_relations_fields());

        apply_filters('affilicious_product_render_affilicious_product_container', $this->post_id, $carbon_container);
        do_action('affilicious_product_after_render');
    }

    /**
     * Get the general fields
     *
     * @since 0.6
     * @return array
     */
    private function get_general_fields()
    {
        $fields = array(
            Carbon_Field::make('select', Carbon_Product_Repository::TYPE, __('Type', 'affilicious'))
                ->add_options(array(
                    'simple' => __('Simple', 'affilicious'),
                    'complex' => __('Complex', 'affilicious'),
                ))
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_general_fields', $fields, $this->post_id);
    }

    /**
     * Get the variants fields
     *
     * @since 0.6
     * @return array
     */
    private function get_variants_fields()
    {
        $fields = array(
            $this->get_attribute_group_choices(
                Carbon_Product_Repository::ATTRIBUTE_GROUP_KEY,
                __('Attribute Group', 'affilicious')
            ),
        );

        $variantComplexFields = $this->get_variants_complex_fields(
            Carbon_Product_Repository::VARIANTS,
            __('Variants', 'affilicious')
        );

        if($variantComplexFields !== null) {
            $fields = array_merge($fields, array($variantComplexFields));
        }

        return apply_filters('affilicious_product_render_affilicious_product_container_variants_fields', $fields, $this->post_id);
    }

    /**
     * Get the complex fields for the variants related to the attribute template group
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return Carbon_Complex_Field
     */
    private function get_variants_complex_fields($name, $label = null)
    {
        $attribute_template_groups = $this->attribute_template_group_repository->find_all();

        if(empty($attribute_template_groups)) {
            return null;
        }

        /** @var Carbon_Complex_Field $field */
        $field = Carbon_Field::make('complex', $name, $label)
            ->setup_labels(array(
                'plural_name' => __('Variants', 'affilicious'),
                'singular_name' => __('Variant', 'affilicious'),
            ));

        foreach ($attribute_template_groups as $attribute_template_group) {
            $title = $attribute_template_group->get_title();
            $key = $attribute_template_group->get_key();

            if (empty($title) || empty($key)) {
                continue;
            }

            $fields = array_merge(array(
                Carbon_Field::make('hidden', Carbon_Product_Repository::VARIANT_ID, __('Variant ID', 'affilicious')),
                Carbon_Field::make('text', Carbon_Product_Repository::VARIANT_TITLE, __('Title', 'affilicious'))
                    ->set_required(true)
                    ->set_width(70),
                Carbon_Field::make('checkbox', Carbon_Product_Repository::VARIANT_DEFAULT, __('Default Variant', 'affilicious'))
                    ->set_option_value('yes')
                    ->help_text(__('This variant will be shown as default for the parent product.', 'affilicious'))
                    ->set_width(30),
                ),
                $this->get_attribute_fields($attribute_template_group),
                array(
                    Carbon_Field::make('image', Carbon_Product_Repository::VARIANT_THUMBNAIL, __('Thumbnail', 'affilicious')),
                    $this->get_shop_tabs(Carbon_Product_Repository::VARIANT_SHOPS, __('Shops', 'affilicious')),
                )
            );

            $field->add_fields($key->get_value(),  $title->get_value(), $fields);

            $field->set_header_template('
                <# if (' . Carbon_Product_Repository::VARIANT_TITLE . ') { #>
                    {{ ' . Carbon_Product_Repository::VARIANT_TITLE . ' }}
                <# } #>
            ');

            $fields[] = $field;
        }

        $field->set_conditional_logic(array(
            'relation' => 'and',
            array(
                'field' => Carbon_Product_Repository::ATTRIBUTE_GROUP_KEY,
                'value' => 'none',
                'compare' => '!=',
            )
        ));

        return $field;
    }

    /**
     * Get the shops fields
     *
     * @since 0.6
     * @return array
     */
    private function get_shops_fields()
    {
        $fields = array(
            $this->get_shop_tabs(
                Carbon_Product_Repository::SHOPS,
                __('Shops', 'affilicious')
            ),
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_shops_fields', $fields, $this->post_id);
    }

    /**
     * Get the details fields
     *
     * @since 0.6
     * @return array
     */
    private function get_details_fields()
    {
        $fields = array(
            $this->get_detail_group_tabs(
                Carbon_Product_Repository::DETAIL_GROUPS,
                __('Detail Groups', 'affilicious')
            )
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_details_fields', $fields, $this->post_id);
    }

    /**
     * Get the review fields
     *
     * @since 0.6
     * @return array
     */
    private function get_review_fields()
    {
        $fields = array(
            Carbon_Field::make('select', Carbon_Product_Repository::REVIEW_RATING, __('Rating', 'affilicious'))
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
            Carbon_Field::make('number', Carbon_Product_Repository::REVIEW_VOTES, __('Votes', 'affilicious'))
                ->set_help_text(__('If you want to hide the votes, just leave it empty.', 'affilicious'))
                ->set_conditional_logic(array(
                    'relation' => 'and',
                    array(
                        'field' => Carbon_Product_Repository::REVIEW_RATING,
                        'value' => 'none',
                        'compare' => '!=',
                    )
                )),
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_review_fields', $fields, $this->post_id);
    }

    /**
     * Get the relation fields
     *
     * @since 0.6
     * @return array
     */
    private function get_relations_fields()
    {
        $fields = array(
            Carbon_Field::make('relationship', Carbon_Product_Repository::RELATED_PRODUCTS, __('Related Products', 'affilicious'))
                ->allow_duplicates(false)
                ->set_post_type(Product::POST_TYPE),
            Carbon_Field::make('relationship', Carbon_Product_Repository::RELATED_ACCESSORIES, __('Related Accessories', 'affilicious'))
                ->allow_duplicates(false)
                ->set_post_type(Product::POST_TYPE),
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_relations_fields', $fields, $this->post_id);
    }

    /**
     * Get the shops tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return Carbon_Complex_Field
     */
    private function get_shop_tabs($name, $label = null)
    {
        $shop_templates = $this->shop_template_repository->find_all();

        /** @var Carbon_Complex_Field $tabs */
        $tabs = Carbon_Field::make('complex', $name, $label)
            ->set_layout('tabbed-horizontal')
            ->setup_labels(array(
                'plural_name' => __('Shops', 'affilicious'),
                'singular_name' => __('Shop', 'affilicious'),
            ));

        foreach ($shop_templates as $shop_template) {
            $fields = array(
                Carbon_Field::make('hidden', Carbon_Product_Repository::SHOP_TEMPLATE_ID, __('Shop Template ID', 'affilicious'))
                    ->set_required(true)
                    ->set_value($shop_template->get_id()->get_value()),
                Carbon_Field::make('text', Carbon_Product_Repository::SHOP_AFFILIATE_LINK, __('Affiliate Link', 'affilicious'))
                    ->set_required(true),
                Carbon_Field::make('text', Carbon_Product_Repository::SHOP_AFFILIATE_ID, __('Affiliate ID', 'affilicious'))
                    ->set_help_text(__('Unique product ID of the shop like Amazon ASIN, Affilinet ID, ebay ID, etc.', 'affilicious')),
                Carbon_Field::make('number', Carbon_Product_Repository::SHOP_PRICE, __('Price', 'affilicious')),
                Carbon_Field::make('number', Carbon_Product_Repository::SHOP_OLD_PRICE, __('Old Price', 'affilicious')),
                Carbon_Field::make('select', Carbon_Product_Repository::SHOP_CURRENCY, __('Currency', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        'euro' => __('Euro', 'affilicious'),
                        'us-dollar' => __('US-Dollar', 'affilicious'),
                    ))
            );

            $tabs->add_fields($shop_template->get_key()->get_value(), $shop_template->get_title()->get_value(), $fields);
        }

        return $tabs;
    }

    /**
     * Get the attribute group choice for the variants
     *
     * @param string $name
     * @param null|string $label
     * @return Carbon_Field
     */
    private function get_attribute_group_choices($name, $label = null)
    {
        $attribute_template_groups = $this->attribute_template_group_repository->find_all();

        $options = array();
        $options['none'] = __('None', 'affilicious');

        foreach ($attribute_template_groups as $attribute_template_group) {
            $title = $attribute_template_group->get_title();
            $key = $attribute_template_group->get_key();

            if (empty($title) || empty($key)) {
                continue;
            }

            $options[$key->get_value()] = $title->get_value();
        }

        $field = Carbon_Field::make('select', $name, $label)
            ->add_options($options);

        return $field;
    }

    /**
     * Get the attribute fields
     *
     * @since 0.6
     * @param Attribute_Template_Group $attribute_template_group
     * @return Carbon_Field[]
     */
    private function get_attribute_fields(Attribute_Template_Group $attribute_template_group)
    {
        $fields = array(
            Carbon_Field::make('hidden', Carbon_Product_Repository::VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID, __('Attribute Template Group ID', 'affilicious'))
                ->set_value($attribute_template_group->get_id()->get_value())
        );

        $attributes = $attribute_template_group->get_attribute_templates();
        foreach ($attributes as $attribute) {
            $field_name = sprintf('%s %s', $attribute->get_title(), $attribute->get_unit());
            $field_name = trim($field_name);

            $field = Carbon_Field::make(
                $attribute->get_type()->get_value(),
                Carbon_Product_Repository::VARIANT_ATTRIBUTE . '_' . $attribute->get_key()->get_value(),
                $field_name
            );

            if ($attribute->has_help_text()) {
                $field->help_text($attribute->get_help_text()->get_value());
            }

            $field->set_required(true);
            $field->set_width(100 / count($attributes));

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Get the detail groups tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @return Carbon_Complex_Field
     */
    private function get_detail_group_tabs($name, $label = null)
    {
        $detail_template_groups = $this->detail_template_group_repository->find_all();

        /** @var Carbon_Complex_Field $tabs */
        $tabs = Carbon_Field::make('complex', $name, $label)
            ->set_layout('tabbed-horizontal')
            ->setup_labels(array(
                'plural_name' => __('Detail Groups', 'affilicious'),
                'singular_name' => __('Detail Group', 'affilicious'),
            ));

        foreach ($detail_template_groups as $detail_template_group) {
            $title = $detail_template_group->get_title()->get_value();
            $key = $detail_template_group->get_key()->get_value();

            if (empty($title) || empty($key)) {
                continue;
            }

            $fields = array();
            $details = $detail_template_group->get_detail_templates();
            foreach ($details as $detail) {
                $field_name = sprintf('%s %s', $detail->get_title(), $detail->get_unit());
                $field_name = trim($field_name);

                $field = Carbon_Field::make(
                    $detail->get_type(),
                    $detail->get_key(),
                    $field_name
                );

                if ($detail->has_help_text()) {
                    $field->help_text($detail->get_help_text()->get_value());
                }

                $fields[] = $field;
            }

            $carbon_detail_group_id = Carbon_Field::make(
                'hidden',
                Carbon_Product_Repository::DETAIL_TEMPLATE_GROUP_ID,
                __('Detail Template Group ID', 'affilicious')
            )->set_value($detail_template_group->get_id()->get_value());

            $fields = array_merge(array(
                Carbon_Product_Repository::DETAIL_TEMPLATE_GROUP_ID => $carbon_detail_group_id,
            ), $fields);

            $tabs->add_fields($key, $title, $fields);
        }

        return $tabs;
    }
}
