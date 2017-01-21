<?php
namespace Affilicious\Common\Helper;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Database_Helper
{
	/**
	 * Converts a text to a key which can be safely stored into the database
	 *
	 * @since 0.6
	 * @param string $text
	 * @return string
	 */
	public static function convert_text_to_key($text)
	{
		$key = str_replace(' ', '_', $text);

		// _names cannot contain underscores followed by digits if you want to support carbon fields
		$key = preg_replace('/_([0-9])/', '$1', $key);

		$key = sanitize_title($key);

		return $key;
	}

    /**
     * Converts the url name to a key which can be safely stored into the database
     *
     * @since 0.6
     * @param string $name
     * @return string
     */
	public static function convert_name_to_key($name)
    {
        $key = str_replace('-', '_', $name);

        // _names cannot contain underscores followed by digits if you want to support carbon fields
        $key = preg_replace('/_([0-9])/', '$1', $key);

        return $key;
    }
}
