<?php
namespace Affilicious\Common\Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Url_Helper
{
	/**
	 * Converts a text to a name which can be safely used in urls
	 *
	 * @since 0.6
	 * @param string $text
	 * @return string
	 */
	public static function convert_text_to_name($text)
	{
		$name = sanitize_title($text);

		return $name;
	}
}
