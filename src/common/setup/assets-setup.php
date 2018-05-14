<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.9
 */
class Assets_Setup
{
    /**
     * Add the admin styles.
     *
     * @hook wp_enqueue_scripts
     * @since 0.9.9
     */
    public function add_styles()
    {
        $base_public_url = \Affilicious::get_root_url() . 'assets/public/dist/';
        $base_vendor_url = \Affilicious::get_root_url() . 'assets/vendor/';

	    // Register Lightslider styles
	    wp_enqueue_style('lightslider', $base_vendor_url . 'lightslider/css/lightslider.min.css', [], '1.1.6');

        // Register universal box styles
        wp_enqueue_style('aff-universal-box', $base_public_url . 'css/universal-box.min.css', ['lightslider'], \Affilicious::VERSION);
    }

    /**
     * Add the admin scripts.
     *
     * @hook wp_enqueue_scripts
     * @since 0.9.9
     */
    public function add_scripts()
    {
	    $base_public_url = \Affilicious::get_root_url() . 'assets/public/dist/';
	    $base_vendor_url = \Affilicious::get_root_url() . 'assets/vendor/';

		// Register Lightslider scripts
	    wp_enqueue_script('lightslider', $base_vendor_url . 'lightslider/js/lightslider.min.js', ['jquery'], '1.1.6', true);

	    // Register universal box scripts
	    wp_enqueue_script('aff-universal-box', $base_public_url . 'js/universal-box.min.js', ['lightslider'], \Affilicious::VERSION, true);
    }
}
