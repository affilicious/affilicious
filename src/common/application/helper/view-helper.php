<?php
namespace Affilicious\Common\Application\Helper;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class View_Helper
{
    /**
     * Render the template immediately.
     *
     * @since 0.7
     * @param string $path The path from the plugin root directory.
     * @param array $params The variables for the template.
     */
    public static function render($path, $params = array())
    {
        $path = \Affilicious_Plugin::get_root_path() . $path;

        // The params are extracted into simple variables
        extract($params);

        /** @noinspection PhpIncludeInspection */
        include($path);
    }

    /**
     * Render the template into a simple string.
     *
     * @since 0.7
     * @param string $path The path from the plugin root directory.
     * @param array $params  The variables for the template.
     * @return string
     */
    public static function stringify($path, $params = array())
    {
        $path = \Affilicious_Plugin::get_root_path() . $path;

        // Every output is converted to a simple string
        ob_start();

        // The params are extracted into simple variables
        extract($params);

        /** @noinspection PhpIncludeInspection */
        include($path);

        $content = ob_get_clean();
        return $content;
    }
}
