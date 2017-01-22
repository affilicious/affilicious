<?php
namespace Affilicious\Common\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Asset_Setup
{
    /**
     * Get the path to the public style directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_public_styles_dir()
    {
        return \Affilicious_Plugin::get_root_url() . '/assets/public/css/';
    }

    /**
     * Get the path to the public script directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_public_script_dir()
    {
        return \Affilicious_Plugin::get_root_url() . '/assets/public/js/';
    }

    /**
     * Get the path to the admin style directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_admin_styles_dir()
    {
        return \Affilicious_Plugin::get_root_url() . '/assets/admin/css/';
    }

    /**
     * Get the path to the admin script directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_admin_script_dir()
    {
        return \Affilicious_Plugin::get_root_url() . '/assets/admin/js/';
    }

    /**
     * Add the public styles for the front end.
     *
     * @since 0.6
     */
    public function add_public_styles()
    {
        wp_enqueue_style('affilicious-public', self::get_public_styles_dir() . 'style.min.css', array(), \Affilicious_Plugin::PLUGIN_VERSION);
    }

    /**
     * Add the admin styles for the back end.
     *
     * @since 0.6
     */
    public function add_admin_styles()
    {
        wp_enqueue_style('affilicious-admin', self::get_admin_styles_dir() . 'admin.min.css', array(), \Affilicious_Plugin::PLUGIN_VERSION);
    }

    /**
     * Add the public scripts for the front end.
     *
     * @since 0.6
     */
    public function add_public_scripts()
    {
        wp_enqueue_script('affilicious-public', self::get_public_script_dir() . 'script.min.js', array('jquery'), \Affilicious_Plugin::PLUGIN_VERSION, true);
    }

    /**
     * Add the admin scripts for the back end.
     *
     * @since 0.6
     */
    public function add_admin_scripts()
    {
        // Localize the script with new data
        $translations = array(
            'container' => __('Affilicious Product', 'affilicious'),
            'variants' => __('Variants', 'affilicious'),
            'shops' => __('Shops', 'affilicious'),
            'addTag' => __('Add', 'affilicious'),
        );

        wp_register_script('affilicious-admin', self::get_admin_script_dir() . 'admin.min.js', array('jquery', 'carbon-fields'), \Affilicious_Plugin::PLUGIN_VERSION, true);
        wp_localize_script('affilicious-admin', 'translations', $translations);
        wp_enqueue_script('affilicious-admin');
    }
}
