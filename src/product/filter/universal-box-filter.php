<?php
namespace Affilicious\Product\Filter;

use Affilicious\Product\Helper\Universal_Mode_Helper;

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
	public function filter($content)
	{
		if(!aff_is_product_page() || !Universal_Mode_Helper::is_enabled()) {
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
