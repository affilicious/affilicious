<?php
namespace Affilicious\Product\Filter;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.3
 */
class Product_Slug_Filter
{
	/**
	 * Filter the product init args to change the default product slug.
	 *
	 * @filter aff_product_init_args
	 * @since 0.10.3
	 * @param array $product_init_args The args used to init the product post type.
	 * @return array The args used to init the product post type.
	 */
	public function filter(array $product_init_args)
	{
		$slug = carbon_get_theme_option('affilicious_options_product_container_general_tab_slug_field');
		if(!empty($slug)) {
			$product_init_args['rewrite']['slug'] = $slug;
		}

		return $product_init_args;
	}
}
