<?php
namespace Affilicious\Product\Setup;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Helper\View_Helper;
use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Repository\Carbon\Carbon_Product_Repository;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;
use Carbon_Fields\Field\Complex_Field as Carbon_Complex_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Setup
{
    const VARIANTS_LIMIT = 50;
    const SHOP_LIMIT = 10;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Detail_Template_Repository_Interface
     */
    private $detail_template_repository;

    /**
     * @var Attribute_Template_Repository_Interface
     */
    private $attribute_template_repository;

    /**
     * @var Key_Generator_Interface
     */
    private $key_generator;

    /**
     * @since 0.6
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Attribute_Template_Repository_Interface $attribute_template_repository
     * @param Detail_Template_Repository_Interface $detail_template_repository
     * @param Key_Generator_Interface $key_generator
     */
    public function __construct(
        Shop_Template_Repository_Interface $shop_template_repository,
        Attribute_Template_Repository_Interface $attribute_template_repository,
        Detail_Template_Repository_Interface $detail_template_repository,
        Key_Generator_Interface $key_generator
    )
    {
        $this->shop_template_repository = $shop_template_repository;
        $this->attribute_template_repository = $attribute_template_repository;
        $this->detail_template_repository = $detail_template_repository;
        $this->key_generator = $key_generator;
    }

    /**
     * @hook init
     * @since 0.6
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
            'all_items'             => $plural,
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
            'label' => $singular,
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
     * @hook init
     * @since 0.6
     */
    public function render()
    {
        do_action('affilicious_product_before_render');

        $shop_templates = $this->shop_template_repository->find_all();
        $attribute_templates = $this->attribute_template_repository->find_all();
        $detail_templates = $this->detail_template_repository->find_all();

        $container = Carbon_Container::make('post_meta', __('Affilicious Product', 'affilicious'))
            ->show_on_post_type(Product::POST_TYPE)
            ->set_priority('core')
            ->add_tab(__('General', 'affilicious'), $this->get_general_fields())
            ->add_tab(__('Variants', 'affilicious'), $this->get_variants_fields($attribute_templates, $shop_templates))
            ->add_tab(__('Shops', 'affilicious'), $this->get_shops_fields($shop_templates))
            ->add_tab(__('Details', 'affilicious'), $this->get_detail_fields($detail_templates))
            ->add_tab(__('Review', 'affilicious'), $this->get_review_fields())
            ->add_tab(__('Relations', 'affilicious'), $this->get_relations_fields());

        apply_filters('affilicious_product_render_affilicious_product_container', $container);
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
                )),
            Carbon_Field::make('tags', Carbon_Product_Repository::TAGS, __('Tags', 'affilicious'))
                ->set_help_text(__('Custom product tags like "test winner" or "best price".', 'affilicious'))
                ->set_conditional_logic(array(
                    'relation' => 'and',
                    array(
                        'field' => Carbon_Product_Repository::TYPE,
                        'value' => 'complex',
                        'compare' => '!=',
                    )
                )),
        );

        return apply_filters('affilicious_product_render_affilicious_product_container_general_fields', $fields);
    }

    /**
     * Get the variants fields
     *
     * @since 0.6
     * @param Attribute_Template[] $attribute_templates
     * @param Shop_Template[] $shop_templates
     * @return array
     */
    private function get_variants_fields($attribute_templates, $shop_templates)
    {
        $fields = array();

        if(empty($attribute_templates)) {
            $fields[] = $this->get_variants_empty_attributes_notice_field();

            return $fields;
        }

        $conditions = array('relation' => 'or');
        foreach ($attribute_templates as $attribute_template) {
            $conditions[] = array(
                'field' => Carbon_Product_Repository::ENABLED_ATTRIBUTES,
                'value' => $attribute_template->get_name()->get_value(),
                'compare' => 'CONTAINS',
            );
        }

        $fields[] = Carbon_Field::make('tags', Carbon_Product_Repository::ENABLED_ATTRIBUTES, __('Enabled Attributes', 'affilicious'))
            ->set_help_text(sprintf(
                __('Add the names of some <a href="%s" target="_blank">attribute templates</a> to attach them to the product variants.', 'affilicious'),
                admin_url('edit-tags.php?taxonomy=aff_attribute_tmpl&post_type=aff_product')
            ));

        $fields[] = Carbon_Field::make('complex', Carbon_Product_Repository::VARIANTS, __('Variants', 'affilicious'))
            ->set_max(self::VARIANTS_LIMIT)
            ->setup_labels(array(
                'plural_name' => __('Variants', 'affilicious'),
                'singular_name' => __('Variant', 'affilicious'),
            ))
            ->add_fields($this->get_variant_field($attribute_templates, $shop_templates))
            ->set_header_template('
                <# if (' . Carbon_Product_Repository::VARIANT_NAME . ') { #>
                    {{ ' . Carbon_Product_Repository::VARIANT_NAME . ' }}
                <# } #>
            ')
            ->set_conditional_logic($conditions);

        return apply_filters('affilicious_product_render_affilicious_product_container_variants_fields', $fields);
    }

    /**
     * Get a single variant field.
     *
     * @since 0.8
     * @param Attribute_Template[] $attribute_templates
     * @param Shop_Template[] $shop_templates
     * @return \Carbon_Fields\Field[]
     */
    public function get_variant_field($attribute_templates, $shop_templates)
    {
        $field = array_merge(array(
            Carbon_Field::make('hidden', Carbon_Product_Repository::VARIANT_ID),
            Carbon_Field::make('hidden', Carbon_Product_Repository::VARIANT_ENABLED_ATTRIBUTES),
            Carbon_Field::make('text', Carbon_Product_Repository::VARIANT_NAME, __('Name', 'affilicious'))
                ->set_required(true)
                ->set_width(70),
            Carbon_Field::make('checkbox', Carbon_Product_Repository::VARIANT_DEFAULT, __('Default Variant', 'affilicious'))
                ->set_option_value('yes')
                ->help_text(__('This variant will be shown as default for the parent product.', 'affilicious'))
                ->set_width(30),
            ),
            $this->get_variants_attribute_fields($attribute_templates),
            array(
                Carbon_Field::make('tags', Carbon_Product_Repository::VARIANT_TAGS, __('Tags', 'affilicious'))
                    ->set_help_text(__('Custom product tags like "test winner" or "best price".', 'affilicious')),
                Carbon_Field::make('image', Carbon_Product_Repository::VARIANT_THUMBNAIL_ID, __('Thumbnail', 'affilicious')),
                Carbon_Field::make('image_gallery', Carbon_Product_Repository::VARIANT_IMAGE_GALLERY, __('Image Gallery', 'affilicious')),
                !empty($shop_templates) ? $this->get_shop_tabs(Carbon_Product_Repository::VARIANT_SHOPS, __('Shops', 'affilicious'), $shop_templates) : $this->get_shops_empty_notice_field(),
            )
        );

        return $field;
    }

    /**
     * Get the attribute fields for the variants.
     *
     * @since 0.8
     * @param Attribute_Template[] $attribute_templates
     * @return array
     */
    public function get_variants_attribute_fields($attribute_templates)
    {
        $fields = array();

        foreach ($attribute_templates as $attribute_template) {
            $fields[] = $this->get_variants_attribute_field($attribute_template);
        }

        return $fields;
    }

    /**
     * Get a single attribute field for the variants.
     *
     * @since 0.8
     * @param Attribute_Template $attribute_template
     * @return mixed
     */
    public function get_variants_attribute_field(Attribute_Template $attribute_template)
    {
        // Build the key
        $attribute_key = $this->key_generator->generate_from_slug($attribute_template->get_slug());
        $field_key = sprintf(Carbon_Product_Repository::VARIANT_ATTRIBUTE_VALUE, $attribute_key->get_value());

        // Build the name
        $field_name = trim(sprintf('%s %s', $attribute_template->get_name(), $attribute_template->get_unit()));

        // Build the type
        $field_type = $attribute_template->get_type()->get_value();

        $field = Carbon_Field::make($field_type, $field_key, $field_name)->set_required(true)
            ->set_required(true)
            ->set_conditional_logic(array(
                'relation' => 'and',
                array(
                    'field' => Carbon_Product_Repository::VARIANT_ENABLED_ATTRIBUTES,
                    'value' => $attribute_template->get_name()->get_value(),
                    'compare' => 'CONTAINS',
                )
            ));

        return $field;
    }

    /**
     * Get the empty attributes notice for the variants.
     *
     * @since 0.8
     * @return Carbon_Field
     */
    public function get_variants_empty_attributes_notice_field()
    {
        $notice = View_Helper::stringify('src/common/view/notifications/warning-notice.php', array(
            'message' => sprintf(
                __('<b>No attribute templates available!</b> Please create one <a href="%s" target="_blank">here</a>.', 'affilicious'),
                admin_url('edit-tags.php?taxonomy=aff_attribute_tmpl&post_type=aff_product')
            )
        ));

        $field = Carbon_Field::make('html', 'affilicious_product_no_attributes')->set_html($notice);

        return $field;
    }

    /**
     * Get the shops fields
     *
     * @since 0.6
     * @param Shop_Template[] $shop_templates
     * @return array
     */
    private function get_shops_fields($shop_templates)
    {
        $fields = array();

        if(empty($shop_templates)) {
            $fields[] = $this->get_shops_empty_notice_field();

            return $fields;
        }

        $fields[] = $this->get_shop_tabs(Carbon_Product_Repository::SHOPS, __('Shops', 'affilicious'), $shop_templates);

        return apply_filters('affilicious_product_render_affilicious_product_container_shops_fields', $fields);
    }

    /**
     * Get the empty notice for the shops.
     *
     * @since 0.8
     * @return Carbon_Field
     */
    private function get_shops_empty_notice_field()
    {
        $notice = $notice = View_Helper::stringify('src/common/view/notifications/warning-notice.php', array(
            'message' => sprintf(
                __('<b>No shop templates available!</b> Please create one <a href="%s" target="_blank">here</a>.', 'affilicious'),
                admin_url('edit-tags.php?taxonomy=aff_shop_tmpl&post_type=aff_product')
            )
        ));

        $fields = Carbon_Field::make('html', 'affilicious_product_no_shops')->set_html($notice);

        return $fields;
    }

    /**
     * Get the shops tabs
     *
     * @since 0.6
     * @param string $name
     * @param null|string $label
     * @param Shop_Template[] $shop_templates
     * @return Carbon_Complex_Field
     */
    private function get_shop_tabs($name, $label = null, $shop_templates)
    {
        /** @var Carbon_Complex_Field $tabs */
        $tabs = Carbon_Field::make('complex', $name, $label)
            ->set_layout('tabbed-horizontal')
            ->set_max(self::SHOP_LIMIT)
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
                    ->set_help_text(__('Unique Product ID like Amazon ASIN, Affilinet ID, Ebay ID, etc. used for the automatic shop update.', 'affilicious')),
                Carbon_Field::make('select', Carbon_Product_Repository::SHOP_AVAILABILITY, __('Availability', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        'available' => __('Available', 'affilicious'),
                        'out-of-stock' => __('Out Of Stock', 'affilicious'),
                    )),
                Carbon_Field::make('text', Carbon_Product_Repository::SHOP_PRICE, __('Price', 'affilicious'))
                    ->set_width(50),
                Carbon_Field::make('text', Carbon_Product_Repository::SHOP_OLD_PRICE, __('Old Price', 'affilicious'))
                    ->set_width(50),
                Carbon_Field::make('select', Carbon_Product_Repository::SHOP_CURRENCY, __('Currency', 'affilicious'))
                    ->set_required(true)
                    ->add_options(array(
                        'EUR' => __('Euro', 'affilicious'),
                        'USD' => __('US-Dollar', 'affilicious'),
                    )),
                Carbon_Field::make('hidden', Carbon_Product_Repository::SHOP_UPDATED_AT, __('Updated At', 'affilicious'))
                    ->set_default_value(current_time('timestamp'))
                    ->set_required(true)
            );

            $tabs->add_fields($this->key_generator->generate_from_slug($shop_template->get_slug())->get_value(), $shop_template->get_name()->get_value(), $fields);
        }

        return $tabs;
    }

    /**
     * Get the details fields.
     *
     * @since 0.6
     * @param Detail_Template[] $detail_templates
     * @return array
     */
    private function get_detail_fields($detail_templates)
    {
        $fields = array();

        if(empty($detail_templates)) {
            $fields[] = $this->get_details_empty_notice_field();

            return $fields;
        }

        $fields[] = Carbon_Field::make('tags', Carbon_Product_Repository::ENABLED_DETAILS, __('Enabled Details', 'affilicious'))
            ->add_class('aff_details')
            ->set_help_text(sprintf(
                __('Add the names of some <a href="%s" target="_blank">detail templates</a> to attach them to the product.', 'affilicious'),
                admin_url('edit-tags.php?taxonomy=aff_detail_tmpl&post_type=aff_product')
            ));

        foreach ($detail_templates as $detail_template) {
            $fields[] = $this->get_detail_field($detail_template);
        }

        return apply_filters('affilicious_product_render_affilicious_product_container_detail_fields', $fields);
    }

    /**
     * Get a single detail field.
     *
     * @since 0.8
     * @param Detail_Template $detail_template
     * @return Carbon_Field\
     */
    private function get_detail_field(Detail_Template $detail_template)
    {
        // Build the key
        $detail_key = $this->key_generator->generate_from_slug($detail_template->get_slug());
        $field_key = sprintf(Carbon_Product_Repository::DETAIL_VALUE, $detail_key);

        // Build the name
        $field_name = trim(sprintf('%s %s', $detail_template->get_name(), $detail_template->get_unit()));

        // Build the type
        $field_type = $detail_template->get_type()->get_value();

        $field = Carbon_Field::make($field_type, $field_key, $field_name)
            ->set_conditional_logic(array(
                'relation' => 'and',
                array(
                    'field' => Carbon_Product_Repository::ENABLED_DETAILS,
                    'value' => $detail_template->get_name()->get_value(),
                    'compare' => 'CONTAINS',
                )
            ));

        return $field;
    }

    /**
     * Get the empty notice field for thr details.
     *
     * @since 0.8
     * @return Carbon_Field
     */
    private function get_details_empty_notice_field()
    {
        $notice =  View_Helper::stringify('src/common/view/notifications/warning-notice.php', array(
            'message' => sprintf(
                __('<b>No detail templates available!</b> Please create one <a href="%s" target="_blank">here</a>.', 'affilicious'),
                admin_url('edit-tags.php?taxonomy=aff_detail_tmpl&post_type=aff_product')
            )
        ));

        $field = Carbon_Field::make('html', 'affilicious_product_no_detail_templates')->set_html($notice);

        return $field;
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

        return apply_filters('affilicious_product_render_affilicious_product_container_review_fields', $fields);
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

        // Remove the current post from the final result
        add_filter('carbon_relationship_options', function($options, $name) {
            if($name == Carbon_Product_Repository::RELATED_PRODUCTS || $name = Carbon_Product_Repository::RELATED_ACCESSORIES) {
                $current_post = get_post();

                foreach ($options as $key => $option) {
                    if($option['id'] == $current_post->ID || $option['is_trashed'] != false || $option['subtype'] != Product::POST_TYPE) {
                        unset($options[$key]);
                    }
                }
            }

            return $options;
        }, 10, 2);

        return apply_filters('affilicious_product_render_affilicious_product_container_relations_fields', $fields);
    }
}
