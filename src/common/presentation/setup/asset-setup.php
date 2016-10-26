<?php
namespace Affilicious\Common\Presentation\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Asset_Setup
{
    /**
     * Get the path to the style directory
     *
     * @since 0.3
     * @return string
     */
    public static function get_styles_dir()
    {
        return \Affilicious_Plugin::get_root_dir() . '/assets/css/';
    }

    /**
     * Get the path to the script directory
     *
     * @since 0.3
     * @return string
     */
    public static function get_script_dir()
    {
        return \Affilicious_Plugin::get_root_dir() . '/assets/js/';
    }

    /**
     * Add the public styles for the front end
     *
     * @since 0.3
     */
    public function add_public_styles()
    {
        wp_enqueue_style('affilicious', self::get_styles_dir() . 'style.min.css', array(), \Affilicious_Plugin::PLUGIN_VERSION);
    }

    /**
     * Add the admin styles for the back end
     *
     * @since 0.3
     */
    public function add_admin_styles()
    {
        wp_enqueue_style('affilicious-admin', self::get_styles_dir() . 'admin.min.css', array(), \Affilicious_Plugin::PLUGIN_VERSION);
    }

    /**
     * Add the public scripts for the front end
     *
     * @since 0.3
     */
    public function add_public_scripts()
    {
        wp_enqueue_script('affilicious', self::get_script_dir() . 'script.min.js', array('jquery'), \Affilicious_Plugin::PLUGIN_VERSION, true);
    }

    /**
     * Add the admin scripts for the back end
     *
     * @since 0.3
     */
    public function add_admin_scripts()
    {
        // _localize the script with new data
        $translations = array(
            'container' => __('_affilicious _product', 'affilicious'),
            'variants' => __('_variants', 'affilicious'),
        );

        wp_register_script('affilicious-admin', self::get_script_dir() . 'admin.min.js', array('jquery', 'carbon-fields'), \Affilicious_Plugin::PLUGIN_VERSION, true);
        wp_localize_script('affilicious-admin', 'translations', $translations);
        wp_enqueue_script('affilicious-admin');
    }
}
