<?php
namespace Affilicious\Product\Setup;

use Affilicious\Common\Helper\Network_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Slug_Rewrite_Setup
{
    const OPTION_SETTINGS_PRODUCT_GENERAL_SLUG = 'affilicious_options_product_container_general_tab_slug_field';
    const PRODUCT_SLUG_HAS_CHANGED = 'affilicious_product_slug_has_changed';

    /**
     * @var Product_Setup
     */
    protected $product_setup;

	/**
	 * @var Custom_Taxonomies_Setup
	 */
    protected $custom_taxonomies_setup;

	/**
	 * @since 0.6
	 * @param Product_Setup $product_setup
	 * @param Custom_Taxonomies_Setup $custom_taxonomies_setup
	 */
    public function __construct(Product_Setup $product_setup, Custom_Taxonomies_Setup $custom_taxonomies_setup)
    {
        $this->product_setup = $product_setup;
        $this->custom_taxonomies_setup = $custom_taxonomies_setup;
    }

    /**
     * Apply the rewrite rules on activation
     *
     * @since 0.6
     * @param bool $network_wide Optional. Activate the slug rewrite for the whole multisite. Default: false.
     */
    public function activate($network_wide = false)
    {
    	Network_Helper::for_each_blog(function() {
		    $this->product_setup->init();
		    $this->custom_taxonomies_setup->init();

		    flush_rewrite_rules();
	    }, $network_wide);
    }

    /**
     * Apply the rewrite rules on deactivation
     *
     * @since 0.6
     * @param bool $network_wide Optional. Deactivate the slug rewrite for the whole multisite. Default: false.
     */
    public function deactivate($network_wide = false)
    {
	    Network_Helper::for_each_blog(function() {
		    flush_rewrite_rules($network_wide = false);
	    }, $network_wide);
    }

    /**
     * Prepare the change of the rewrite rules
     *
     * @hook added_option
     * @hook updated_option
     * @since 0.6
     * @param string $option
     */
    public function prepare($option)
    {
        if($option == self::OPTION_SETTINGS_PRODUCT_GENERAL_SLUG) {
            update_option(self::PRODUCT_SLUG_HAS_CHANGED, true);
        }
    }

    /**
     * Apply the change of the rewrite rules
     *
     * @hook init
     * @since 0.6
     */
    public function run()
    {
        if (get_option(self::PRODUCT_SLUG_HAS_CHANGED) == true ) {
            flush_rewrite_rules();
            update_option(self::PRODUCT_SLUG_HAS_CHANGED, false);
        }
    }
}
