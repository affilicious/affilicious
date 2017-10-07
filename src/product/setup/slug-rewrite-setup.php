<?php
namespace Affilicious\Product\Setup;

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
     */
    public function activate()
    {
        $this->product_setup->init();
        $this->custom_taxonomies_setup->init();
        flush_rewrite_rules();
    }

    /**
     * Apply the rewrite rules on deactivation
     *
     * @since 0.6
     */
    public function deactivate()
    {
        flush_rewrite_rules();
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
