<?php
namespace Affilicious\Shop\Setup;

use Affilicious\Common\Setup\Setup_Interface;
use Affilicious\Product\Model\Product_Interface;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Shop_Template;
use Affilicious\Shop\Repository\Carbon\Carbon_Shop_Template_Repository;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template_Setup implements Setup_Interface
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
	 * @inheritdoc
     * @since 0.6
	 */
	public function init()
	{
        do_action('affilicious_shop_template_setup_before_init');

        $singular = __('Shop Template', 'affilicious');
        $plural = __('Shop Templates', 'affilicious');
        $labels = array(
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => __('Shops', 'affilicious'),
            'name_admin_bar'        => $singular,
            'archives'              => sprintf(_x('%s Archives', 'Shop Template', 'affilicious'), $singular),
            'parent_item_colon'     => sprintf(_x('Parent %s:', 'Shop Template', 'affilicious'), $singular),
            'all_items'             => __('Shops', 'affilicious'),
            'add_new_item'          => sprintf(_x('Add New %s', 'Shop Template', 'affilicious'), $singular),
            'new_item'              => sprintf(_x('New %s', 'Shop Template', 'affilicious'), $singular),
            'edit_item'             => sprintf(_x('Edit %s', 'Shop Template', 'affilicious'), $singular),
            'update_item'           => sprintf(_x('Update %s', 'Shop Template', 'affilicious'), $singular),
            'view_item'             => sprintf(_x('View %s', 'Shop Template', 'affilicious'), $singular),
            'search_items'          => sprintf(_x('Search %s', 'Shop Template', 'affilicious'), $singular),
            'insert_into_item'      => sprintf(_x('Insert Into %s', 'Shop Template', 'affilicious'), $singular),
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Shop Template', 'affilicious'), $singular),
            'items_list'            => $plural,
            'items_list_navigation' => sprintf(_x('%s Navigation', 'Shop Template', 'affilicious'), $singular),
            'filter_items_list'     => sprintf(_x('Filter %s', 'Shop Template', 'affilicious'), $plural),
        );

        register_taxonomy(Shop_Template::TAXONOMY,  Product_Interface::POST_TYPE, array(
            'hierarchical'      => false,
            'public'            => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'meta_box_cb'       => false,
            'query_var'         => true,
            'description'       => false,
            'rewrite'           => false,
        ));

        do_action('affilicious_shop_template_setup_after_init');
	}

	/**
	 * @inheritdoc
     * @since 0.8
	 */
	public function render()
	{
        do_action('affilicious_shop_template_setup_before_render');

        $container = Carbon_Container::make('term_meta', __('Shop Template', 'affilicious'))
            ->show_on_taxonomy(Shop_Template::TAXONOMY)
            ->add_fields(array(
                Carbon_Field::make('select', Carbon_Shop_Template_Repository::PROVIDER, __('Provider', 'affilicious'))
                    ->set_required(true)
                    ->add_options($this->get_provider_options())
                    ->set_help_text(__('The provider is used for the automatic updates for products using this shop.', 'affilicious')),
                Carbon_Field::make('image', Carbon_Shop_Template_Repository::THUMBNAIL, __('Logo', 'affilicious'))
                    ->set_help_text(__('The logo is used to show an image near the shop prices in products.', 'affilicious')),
            ));

        apply_filters('affilicious_shop_template_setup_render_shop_template_options_container', $container);
        do_action('affilicious_shop_template_setup_after_render');
	}

    /**
     * Get the options for the provider choice.
     *
     * @since 0.8
     * @return array
     */
	private function get_provider_options()
    {
        $providers = $this->provider_repository->find_all();

        $options = array('none' => __('None', 'affilicious'));
        foreach ($providers as $provider) {
            if ($provider->has_id()) {
                $options[$provider->get_id()->get_value()] = $provider->get_name()->get_value();
            }
        }

        return $options;
    }
}
