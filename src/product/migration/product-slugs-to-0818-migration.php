<?php
namespace Affilicious\Product\Migration;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8.18
 */
final class Product_Slugs_To_0818_Migration
{
	/**
	 * @since 0.8.18
	 * @var string
	 */
    const OPTION = 'aff_migrated_product_slugs_to_0.8.18';

    /**
     * Refresh the slugs to migrate the new hook priorities.
     *
     * @since 0.8.18
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.8.18' && get_option(self::OPTION) != 'yes') {
            flush_rewrite_rules();

            update_option(self::OPTION, 'yes');
        }
    }
}
