<?php
namespace Affilicious\Shop\Application\Setup;

use Affilicious\Common\Application\Setup\Setup_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Repository_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template;
use Affilicious\Shop\Domain\Model\Shop_Template_Interface;
use Affilicious\Shop\Infrastructure\Repository\Carbon\Carbon_Shop_Template_Repository;
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
     * @var int
     */
    private $post_id;

    /**
     * @since 0.7
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
        $this->post_id = isset($_GET['post']) ? $_GET['post'] : null;
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
            'menu_name'             => $singular,
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

		register_post_type(Shop_Template::POST_TYPE, array(
			'labels'          => $labels,
			'public'          => false,
			'menu_icon'       => 'dashicons-cart',
			'supports'        => array('title', 'editor', 'thumbnail', 'revisions'),
			'show_ui'         => true,
			'menu_position'   => 5,
			'capability_type' => 'page',
			'hierarchical'    => true,
			'rewrite'         => false,
			'query_var'       => Shop_Template::POST_TYPE,
			'show_in_menu'    => 'edit.php?post_type=aff_product',
		));

        do_action('affilicious_shop_template_setup_after_init');
	}

	/**
	 * @inheritdoc
     * @since 0.6
	 */
	public function render()
	{
        do_action('affilicious_shop_template_setup_before_render');

        $container = Carbon_Container::make('post_meta', __('Shop Template Options', 'affilicious'))
            ->show_on_post_type(Shop_Template_Interface::POST_TYPE)
            ->set_priority('core')
            ->add_tab(__('Provider', 'affilicious'), $this->get_provider_fields());

        apply_filters('affilicious_shop_template_setup_render_shop_template_options_container', $container, $this->post_id);
        do_action('affilicious_shop_template_setup_after_render');
	}

    /**
     * Get the provider fields
     *
     * @since 0.6
     * @return array
     */
	private function get_provider_fields()
    {
        $fields = array(
            Carbon_Field::make('select', Carbon_Shop_Template_Repository::PROVIDER, __('Provider', 'affilicious'))
                ->set_required(true)
                ->add_options($this->get_provider_options())
                ->set_help_text(__('The provider is used for the automatic updates for products using this shop.', 'affilicious')),
        );

        return apply_filters('affilicious_shop_template_render_shop_template_options_container_provider_fields', $fields, $this->post_id);
    }

    /**
     * Get the options for the provider choice.
     *
     * @since 0.7
     * @return array
     */
	private function get_provider_options()
    {
        $providers = $this->provider_repository->find_all();

        $options = array('none' => __('None', 'affilicious'));
        foreach ($providers as $provider) {
            $options[$provider->get_name()->get_value()] = $provider->get_title()->get_value();
        }

        return $options;
    }
}
