<?php
namespace Affilicious\Common\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.4
 */
class Assets_Setup
{
	/**
	 * @since 0.10.4
	 * @var string
	 */
	const VENDOR_URL = AFFILICIOUS_ROOT_URL . 'assets/vendor/';

    /**
     * Enqueue or register styles and scripts.
     *
     * @hook wp_enqueue_scripts
     * @since 0.10.4
     */
    public function init()
    {
	    // Styles
	    wp_register_style('selectize', self::VENDOR_URL . 'selectize/css/selectize.css', [], '0.12.4');

	    wp_register_style('lightslider', self::VENDOR_URL . 'lightslider/css/lightslider.min.css', [], '1.1.6');

	    // Scripts
	    wp_register_script('selectize', self::VENDOR_URL . 'selectize/js/selectize.min.js', ['jquery'], '0.12.4', true);

	    wp_register_script('lightslider', self::VENDOR_URL . 'lightslider/js/lightslider.min.js', ['jquery'], '1.1.6', true);
    }
}
