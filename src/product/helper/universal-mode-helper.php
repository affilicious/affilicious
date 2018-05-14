<?php
namespace Affilicious\Product\Helper;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.10
 */
class Universal_Mode_Helper
{
	/**
	 * Check if the universal mode is enabled at all.
	 *
	 * @since 0.9.10
	 * @return bool
	 */
	public static function is_enabled()
	{
		return self::is_enabled_for_theme() && self::is_enabled_per_filter();
	}

	/**
	 * Check if the universal mode is enabled per filter.
	 *
	 * @since 0.9.10
	 * @return bool
	 */
	public static function is_enabled_per_filter()
	{
		$enabled = apply_filters('aff_product_universal_mode_enabled', true);
		Assert_Helper::is_boolean($enabled, __METHOD__, 'The universal mode flag has to be a boolean value', '0.9.10');

		return $enabled;
	}

	/**
	 * Check of the universal mode is enabled for the current theme except some specific one like the first Affilivice theme.
	 * This method is important to make the old themes compatible with the universal mode, because
	 * they are not setting the filter "aff_product_universal_mode_enabled" to "false".
	 *
	 * @since 0.9.10
	 * @return bool
	 */
	public static function is_enabled_for_theme()
	{
		$current_theme = wp_get_theme();
		$stylesheet = $current_theme->get_stylesheet();

		return !in_array($stylesheet, ['affilivice']);
	}
}
