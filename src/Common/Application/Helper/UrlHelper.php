<?php
namespace Affilicious\Common\Application\Helper;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class UrlHelper
{
	/**
	 * Converts a text to a name which can be safely used in urls
	 *
	 * @since 0.6
	 * @param string $text
	 * @return string
	 */
	public static function convertTextToName($text)
	{
		$name = sanitize_title($text);

		return $name;
	}
}
