<?php
namespace Affilicious\Common\Application\Helper;

class DatabaseHelper
{
	/**
	 * Converts a text to a key which can be safely stored into the database
	 *
	 * @since 0.5.2
	 * @param string $text
	 * @return string
	 */
	public static function convertTextToKey($text)
	{
		$key = str_replace(' ', '_', $text);

		// Names cannot contain underscores followed by digits if you want to support carbon fields
		$key = preg_replace('/_([0-9])/', '$1', $key);

		$key = sanitize_title($key);

		return $key;
	}
}
