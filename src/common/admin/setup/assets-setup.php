<?php
namespace Affilicious\Common\Admin\Setup;

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
	const ADMIN_URL = AFFILICIOUS_ROOT_URL . '/assets/admin/dist/';

	/**
	 * @since 0.10.4
	 * @var string
	 */
	const VENDOR_URL = AFFILICIOUS_ROOT_URL . 'assets/vendor/';

    /**
     * Enqueue or register admin styles and scripts.
     *
     * @hook admin_enqueue_scripts
     * @since 0.10.4
     */
    public function init()
    {
        // Styles
	    wp_enqueue_style('selectize', self::VENDOR_URL . 'selectize/css/selectize.css', [], '0.12.4');

        wp_enqueue_style('aff-admin-common', self::ADMIN_URL . 'css/common.min.css', [], \Affilicious::VERSION);

        wp_enqueue_style('aff-admin-carbon-fields', self::ADMIN_URL . 'css/carbon-fields.min.css', ['selectize'], \Affilicious::VERSION);

        wp_enqueue_style('aff-admin-import', self::ADMIN_URL . 'css/import.min.css', [], \Affilicious::VERSION);

        // Scripts
	    wp_enqueue_script('selectize', self::VENDOR_URL . 'selectize/js/selectize.min.js', ['jquery'], '0.12.4', true);

        wp_enqueue_script('aff-admin-common', self::ADMIN_URL . 'js/common.min.js', ['jquery'], \Affilicious::VERSION, true);

        wp_enqueue_script('aff-admin-carbon-fields', self::ADMIN_URL . 'js/carbon-fields.min.js', ['jquery', 'selectize', 'carbon-fields'], \Affilicious::VERSION, true);
        wp_localize_script('aff-admin-carbon-fields', 'affCarbonFieldsTranslations', [
            'addTag' => __('Add', 'affilicious'),
        ]);
    }
}
