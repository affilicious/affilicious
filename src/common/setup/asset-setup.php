<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
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
        return \Affilicious::get_root_url() . '/assets/public/css/';
    }

    /**
     * Get the path to the public script directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_public_script_dir()
    {
        return \Affilicious::get_root_url() . '/assets/public/js/';
    }

    /**
     * Get the path to the admin style directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_admin_styles_dir()
    {
        return \Affilicious::get_root_url() . '/assets/admin/css/';
    }

    /**
     * Get the path to the admin script directory.
     *
     * @since 0.7
     * @return string
     */
    public static function get_admin_script_dir()
    {
        return \Affilicious::get_root_url() . '/assets/admin/js/';
    }

    /**
     * Add the public styles for the front end.
     *
     * @hook wp_enqueue_scripts
     * @since 0.6
     */
    public function add_public_styles()
    {
    }

    /**
     * Add the admin styles for the back end.
     *
     * @hook admin_enqueue_scripts
     * @since 0.6
     */
    public function add_admin_styles()
    {
        wp_enqueue_style('affilicious-admin', self::get_admin_styles_dir() . 'admin.min.css', array(), \Affilicious::VERSION);
    }

    /**
     * Add the public scripts for the front end.
     *
     * @hook wp_enqueue_scripts
     * @since 0.6
     */
    public function add_public_scripts()
    {
    }

    /**
     * Add the admin scripts for the back end.
     *
     * @hook admin_enqueue_scripts
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
            'ajaxUrl' => admin_url('admin-ajax.php')
        );

        wp_register_script('aff-admin-amazon-import', \Affilicious::get_root_url() . '/assets/admin/dist/js/amazon-import.min.js', array('jquery', 'carbon-fields'), \Affilicious::VERSION, true);
        wp_localize_script('aff-admin-amazon-import', 'translations', $translations);
        wp_enqueue_script('aff-admin-amazon-import');
    }
}
