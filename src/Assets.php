<?php
namespace Affilicious\ProductsPlugin;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Assets
{
    /**
     * Add the public styles for the front end
     */
    public function addPublicStyles()
    {
        wp_enqueue_style('affilicious-products', \AffiliciousProductsPlugin::getRootDir() . '/assets/css/style.css', array(), '0.1.0');
    }

    /**
     * Add the admin styles for the back end
     */
    public function addAdminStyles()
    {
        wp_enqueue_style('affilicious-products-admin', \AffiliciousProductsPlugin::getRootDir() . '/assets/css/admin.css', array(), '0.1.0');
    }

    /**
     * Add the public scripts for the front end
     */
    public function addPublicScripts()
    {
        wp_enqueue_script('affilicious-products', \AffiliciousProductsPlugin::getRootDir() . '/assets/js/script.js', array('jquery'), '0.1.0', true);
    }

    /**
     * Add the admin scripts for the back end
     */
    public function addAdminScripts()
    {
        wp_enqueue_script('affilicious-products-admin', \AffiliciousProductsPlugin::getRootDir() . '/assets/js/admin.js', array('jquery'), '0.1.0', true);
    }
}
