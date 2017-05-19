<?php
namespace Affilicious\Common\Admin\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Assets_Setup
{
    /**
     * Add the admin styles.
     *
     * @hook admin_enqueue_scripts
     * @since 0.9
     */
    public function add_styles()
    {
        $base_vendor_url = \Affilicious::get_root_url() . '/assets/vendor/';
        $base_admin_url = \Affilicious::get_root_url() . '/assets/admin/dist/css/';

        // Register jQuery tags input styles
        wp_register_style('jquery-tags-input', $base_vendor_url . 'jquery-tags-input/css/jquery.tagsInput.min.css', [], '1.3.3');

        // Register common styles
        wp_register_style('aff-admin-common', $base_admin_url  . 'common.min.css', [], \Affilicious::VERSION);

        // Register products styles
        wp_register_style('aff-admin-products', $base_admin_url  . 'products.min.css', [], \Affilicious::VERSION);

        // Register Carbon Fields styles
        wp_register_style('aff-admin-carbon-fields', $base_admin_url . 'carbon-fields.min.css', ['jquery-tags-input'], \Affilicious::VERSION);

        // Enqueue the styles
        wp_enqueue_style('jquery-tags-input');
        wp_enqueue_style('aff-admin-common');
        wp_enqueue_style('aff-admin-products');
        wp_enqueue_style('aff-admin-carbon-fields');
    }

    /**
     * Add the admin scripts.
     *
     * @hook admin_enqueue_scripts
     * @since 0.9
     */
    public function add_scripts()
    {
        $base_vendor_url = \Affilicious::get_root_url() . '/assets/vendor/';
        $base_admin_url = \Affilicious::get_root_url() . '/assets/admin/dist/js/';

        // Register jQuery tags input scripts
        wp_register_script('jquery-tags-input', $base_vendor_url . 'jquery-tags-input/js/jquery.tagsInput.min.js', [], '1.3.3');

        // Register products scripts
        wp_register_script('aff-admin-products', $base_admin_url  . 'products.min.js', ['jquery', 'carbon-fields'], \Affilicious::PLUGIN_VERSION, true);
        wp_localize_script('aff-admin-products', 'affProductTranslations', array(
            'container' => __('Affilicious Product', 'affilicious'),
            'variants' => __('Variants', 'affilicious'),
            'shops' => __('Shops', 'affilicious'),
        ));

        // Register Carbon Fields scripts
        wp_register_script('aff-admin-carbon-fields', $base_admin_url . 'carbon-fields.min.js', ['jquery', 'jquery-tags-input', 'carbon-fields'], \Affilicious::PLUGIN_VERSION, true);
        wp_localize_script('aff-admin-carbon-fields', 'affCarbonFieldsTranslations', [
            'addTag' => __('Add', 'affilicious'),
        ]);

        // Register Amazon import scripts
        wp_register_script('aff-admin-amazon-import', $base_admin_url  . 'amazon-import.min.js', ['jquery', 'backbone'], \Affilicious::PLUGIN_VERSION, true);

        // Enqueue the scripts
        wp_enqueue_script('jquery-tags-input');
        wp_enqueue_script('aff-admin-products');
        wp_enqueue_script('aff-admin-carbon-fields');
        wp_enqueue_script('aff-admin-amazon-import');
    }
}
