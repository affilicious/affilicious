<?php
namespace Affilicious\Product\Admin\Setup;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.4
 */
class Assets_Setup
{
	/**
	 * @since 0.10.4
	 * @var string
	 */
	const ADMIN_URL = AFFILICIOUS_ROOT_URL . '/assets/admin/dist/';

	/**
	 * Enqueue or register admin styles and scripts.
	 *
	 * @hook admin_enqueue_scripts
	 * @since 0.10.4
	 */
	public function init()
	{
		// Styles
		wp_enqueue_style('aff-admin-products', self::ADMIN_URL . 'css/products.min.css', [], \Affilicious::VERSION);

		// Scripts
		wp_enqueue_script('aff-admin-products', self::ADMIN_URL . 'js/products.min.js', ['jquery', 'carbon-fields'], \Affilicious::VERSION, true);
		wp_localize_script('aff-admin-products', 'affProductTranslations', array(
			'container' => __('Affilicious Product', 'affilicious'),
			'variants' => __('Variants', 'affilicious'),
			'shops' => __('Shops', 'affilicious'),
		));
	}
}
