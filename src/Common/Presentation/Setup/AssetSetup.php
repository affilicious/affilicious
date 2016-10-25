<?php
namespace Affilicious\Common\Presentation\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AssetSetup
{
    /**
     * Get the path to the style directory
     *
     * @since 0.3
     * @return string
     */
    public static function getStylesDir()
    {
        return \AffiliciousPlugin::getRootDir() . '/assets/css/';
    }

    /**
     * Get the path to the script directory
     *
     * @since 0.3
     * @return string
     */
    public static function getScriptDir()
    {
        return \AffiliciousPlugin::getRootDir() . '/assets/js/';
    }

    /**
     * Add the public styles for the front end
     *
     * @since 0.3
     */
    public function addPublicStyles()
    {
        wp_enqueue_style('affilicious', self::getStylesDir() . 'style.min.css', array(), \AffiliciousPlugin::PLUGIN_VERSION);
    }

    /**
     * Add the admin styles for the back end
     *
     * @since 0.3
     */
    public function addAdminStyles()
    {
        wp_enqueue_style('affilicious-admin', self::getStylesDir() . 'admin.min.css', array(), \AffiliciousPlugin::PLUGIN_VERSION);
    }

    /**
     * Add the public scripts for the front end
     *
     * @since 0.3
     */
    public function addPublicScripts()
    {
        wp_enqueue_script('affilicious', self::getScriptDir() . 'script.min.js', array('jquery'), \AffiliciousPlugin::PLUGIN_VERSION, true);
    }

    /**
     * Add the admin scripts for the back end
     *
     * @since 0.3
     */
    public function addAdminScripts()
    {
        // Localize the script with new data
        $translations = array(
            'container' => __('Affilicious Product', 'affilicious'),
            'variants' => __('Variants', 'affilicious'),
        );

        wp_register_script('affilicious-admin', self::getScriptDir() . 'admin.min.js', array('jquery', 'carbon-fields'), \AffiliciousPlugin::PLUGIN_VERSION, true);
        wp_localize_script('affilicious-admin', 'translations', $translations);
        wp_enqueue_script('affilicious-admin');
    }
}
