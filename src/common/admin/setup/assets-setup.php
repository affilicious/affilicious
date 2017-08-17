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
        $screen = get_current_screen();

        $base_vendor_url = \Affilicious::get_root_url() . '/assets/vendor/';
        $base_admin_url = \Affilicious::get_root_url() . '/assets/admin/dist/css/';

        // Register Selectize styles
        wp_register_style('selectize', $base_vendor_url . 'selectize/css/selectize.css', [], '0.12.4');

        // Register common styles
        wp_register_style('aff-admin-common', $base_admin_url  . 'common.min.css', [], \Affilicious::VERSION);

        // Register products styles
        wp_register_style('aff-admin-products', $base_admin_url  . 'products.min.css', [], \Affilicious::VERSION);

        // Register Carbon Fields styles
        wp_register_style('aff-admin-carbon-fields', $base_admin_url . 'carbon-fields.min.css', ['selectize'], \Affilicious::VERSION);

        // Register Amazon import styles
        //if($screen->id == 'aff_product_page_import') {
            wp_register_style('aff-admin-amazon-import', $base_admin_url . 'amazon-import.min.css', [], \Affilicious::VERSION);
        //}

        // Enqueue the styles
        wp_enqueue_style('selectize');
        wp_enqueue_style('aff-admin-common');
        wp_enqueue_style('aff-admin-products');
        wp_enqueue_style('aff-admin-carbon-fields');

        //if($screen->id == 'aff_product_page_import') {
            wp_enqueue_style('aff-admin-amazon-import');
        //}
    }

    /**
     * Add the admin scripts.
     *
     * @hook admin_enqueue_scripts
     * @since 0.9
     */
    public function add_scripts()
    {
        $screen = get_current_screen();

        $base_vendor_url = \Affilicious::get_root_url() . '/assets/vendor/';
        $base_admin_url = \Affilicious::get_root_url() . '/assets/admin/dist/js/';

        // Register Selectize scripts
        wp_register_script('selectize', $base_vendor_url . 'selectize/js/selectize.min.js', [], '0.12.4');

        // Register products scripts
        wp_register_script('aff-admin-products', $base_admin_url  . 'products.min.js', ['jquery', 'carbon-fields'], \Affilicious::VERSION, true);
        wp_localize_script('aff-admin-products', 'affProductTranslations', array(
            'container' => __('Affilicious Product', 'affilicious'),
            'variants' => __('Variants', 'affilicious'),
            'shops' => __('Shops', 'affilicious'),
        ));

        // Register Carbon Fields scripts
        wp_register_script('aff-admin-carbon-fields', $base_admin_url . 'carbon-fields.min.js', ['jquery', 'selectize', 'carbon-fields'], \Affilicious::VERSION, true);
        wp_localize_script('aff-admin-carbon-fields', 'affCarbonFieldsTranslations', [
            'addTag' => __('Add', 'affilicious'),
        ]);

        // Register Amazon import scripts
	    if($screen->id == 'aff_product_page_aff-import-amazon') {
            wp_register_script('aff-admin-amazon-import', $base_admin_url . 'amazon-import.min.js', ['jquery', 'backbone'], \Affilicious::VERSION, true);
            wp_localize_script('aff-admin-amazon-import', 'affAdminAmazonImportUrls', [
                'ajax' => admin_url('admin-ajax.php'),
                'apiRoot' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest')
            ]);
        }

        // Enqueue the scripts
        wp_enqueue_script('selectize');
        wp_enqueue_script('aff-admin-products');
        wp_enqueue_script('aff-admin-carbon-fields');

        if($screen->id == 'aff_product_page_aff-import-amazon') {
            wp_enqueue_script('aff-admin-amazon-import');
        }
    }
}
