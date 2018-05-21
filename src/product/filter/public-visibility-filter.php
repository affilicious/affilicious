<?php
namespace Affilicious\Product\Filter;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.3
 */
class Public_Visibility_Filter
{
	/**
	 * Filter the product init args to show or hide products in the frontend.
	 *
	 * @filter aff_product_init_args
	 * @since 0.10.3
	 * @param array $product_init_args The args used to init the product post type.
	 * @return array The args used to init the product post type.
	 */
	public function filter(array $product_init_args)
	{
		$disable_public_visibility = carbon_get_theme_option('affilicious_options_product_container_general_tab_disable_public_visibility_field');
		if($disable_public_visibility == 'yes') {
			$product_init_args['public'] = false;
		}

		return $product_init_args;
	}
}
