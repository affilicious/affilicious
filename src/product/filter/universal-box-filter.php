<?php
namespace Affilicious\Product\Filter;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Universal_Box_Filter
{
	/**
	 * Filter the content and add the universal box
	 *
	 * @filter the_content
	 * @since 0.9.10
	 * @param string $content
	 * @return string
	 */
	function filter($content)
	{
		if(!aff_is_product_page()) {
			return $content;
		}

		$product = aff_get_product();
		if(empty($product)) {
			return $content;
		}

		$universal_box = aff_stringify_template('product/universal-box', [
			'product' => $product
		]);

		return $content . $universal_box;
	}
}
