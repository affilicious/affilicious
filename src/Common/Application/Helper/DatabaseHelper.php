<?php
namespace Affilicious\Common\Application\Helper;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DatabaseHelper
{
	/**
	 * Converts a text to a key which can be safely stored into the database
	 *
	 * @since 0.6
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

    /**
     * Converts the url name to a key which can be safely stored into the database
     *
     * @since 0.6
     * @param string $name
     * @return string
     */
	public static function convertNameToKey($name)
    {
        $key = str_replace('-', '_', $name);

        // Names cannot contain underscores followed by digits if you want to support carbon fields
        $key = preg_replace('/_([0-9])/', '$1', $key);

        return $key;
    }
}
