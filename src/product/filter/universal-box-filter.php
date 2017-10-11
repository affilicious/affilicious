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

		// Get the current product.
		$product = aff_get_product();
		if(empty($product)) {
			return $content;
		}

		// Get the box HTML.
		$universal_box = aff_stringify_template('product/universal-box', [
			'product' => $product
		]);

		// Check if the box is disabled per option.
		if($this->is_disabled()) {
			return $content;
		}

		// Get the content position of the box.
		if($this->get_content_position() == 'above') {
			$content = $universal_box . $content;
		} else {
			$content = $content .  $universal_box;
		}

		return $content;
	}

	/**
	 * Check whether the universal box is disabled or not.
	 *
	 * @since 0.9.10
	 * @return bool Whether the universal box is disabled or not.
	 */
	protected function is_disabled()
	{
		$disabled = carbon_get_theme_option('affilicious_options_product_container_universal_box_tab_disabled_field');
		$disabled = !empty($disabled) ? true : false;

		return $disabled;
	}

	/**
	 * Get the universal box content position.
	 *
	 * @since 0.9.10
	 * @return string Whether the universal box is "above" or "below" the content.
	 */
	protected function get_content_position()
	{
		$position = carbon_get_theme_option('affilicious_options_product_container_universal_box_tab_position_field');
		if(empty($position)) {
			$position = 'below';
		}

		return $position;
	}
}
