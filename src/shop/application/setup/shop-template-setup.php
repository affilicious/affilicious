<?php
namespace Affilicious\Shop\Application\Setup;

use Affilicious\Common\Application\Setup\Setup_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template_Setup implements Setup_Interface
{
	/**
	 * @inheritdoc
     * @since 0.6
	 */
	public function init()
	{
        do_action('affilicious_shop_template_before_init');

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
			'show_in_menu'    => 'edit.php?post_type=product',
		));

        do_action('affilicious_shop_template_after_init');
	}

	/**
	 * @inheritdoc
     * @since 0.6
	 */
	public function render()
	{
        do_action('affilicious_shop_template_before_render');

		// _nothing to do here yet

        do_action('affilicious_shop_template_after_render');
	}

	/**
	 * Add a column header for the logo
	 *
	 * @since 0.6
	 * @param array $defaults
	 * @return array
	 */
	public function columns_head($defaults)
	{
		$new = array();
		foreach ($defaults as $key => $title) {
			// _put the logo column before the date column
			if ($key == 'date') {
				$new['logo'] = __('Featured Image');
			}
			$new[$key] = $title;
		}

		return $new;
	}

	/**
	 * Add a column for the logo
	 *
	 * @since 0.6
	 * @param string $column_name
	 * @param int $shop_id
	 */
	public function columns_content($column_name, $shop_id)
	{
		if ($column_name == 'logo') {
            $shop_logo_id = get_post_thumbnail_id($shop_id);
            if (!$shop_logo_id) {
                return;
            }

            $shop_logo = wp_get_attachment_image_src($shop_logo_id, 'featured_preview');
			if ($shop_logo) {
				echo '<img src="' . $shop_logo[0] . '" />';
			}
		}
	}
}
