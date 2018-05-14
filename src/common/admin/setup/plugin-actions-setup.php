<?php
namespace Affilicious\Common\Admin\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.16
 */
class Plugin_Actions_Setup
{
	/**
	 * @since 0.9.16
	 * @var string
	 */
    const ADDONS_URL = 'https://affilicious.com/downloads/category/erweiterungen/?utm_campaign=addons&utm_source=wordpress-installation&utm_medium=plugin-actions';

    /**
     * @since 0.9.16
     * @param array $links
     * @return array
     */
    public function init(array $links)
    {
        $settings_link = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=crbn-affilicious.php'), __('Settings', 'affilicious'));

        array_unshift($links, $settings_link);

        $links['addons'] = sprintf('<a href="%s" target="_blank" class="aff-plugin-actions-addons">%s</a>', self::ADDONS_URL, __('Addons', 'affilicious'));

        return $links;
    }
}
