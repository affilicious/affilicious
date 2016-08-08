<?php
namespace Affilicious\ProductsPlugin\Common\Application\Setup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AssetSetup
{
    /**
     * Get the path to the style directory
     * @return string
     */
    public static function getStylesDir()
    {
        return \AffiliciousProductsPlugin::getRootDir() . '/assets/css/';
    }

    /**
     * Get the path to the script directory
     * @return string
     */
    public static function getScriptDir()
    {
        return \AffiliciousProductsPlugin::getRootDir() . '/assets/js/';
    }

    /**
     * Add the public styles for the front end
     */
    public function addPublicStyles()
    {
        wp_enqueue_style('affilicious-products', self::getStylesDir() . 'style.css', array(), '0.3.0');
    }

    /**
     * Add the admin styles for the back end
     */
    public function addAdminStyles()
    {
        wp_enqueue_style('affilicious-products-admin', self::getStylesDir() . 'admin.css', array(), '0.3.0');
    }

    /**
     * Add the public scripts for the front end
     */
    public function addPublicScripts()
    {
        wp_enqueue_script('affilicious-products', self::getScriptDir() . 'script.js', array('jquery'), '0.3.0', true);
    }

    /**
     * Add the admin scripts for the back end
     */
    public function addAdminScripts()
    {
        wp_enqueue_script('affilicious-products-admin', self::getScriptDir() . 'admin.js', array('jquery'), '0.3.0', true);
    }
}
