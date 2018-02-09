<?php
namespace Affilicious\Product\Migration;

use Affilicious\Product\Cleaner\Orphaned_Product_Variants_Cleaner_Timer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Orphaned_Product_Variants_Cleaner_Timer_to_0922_Migration
{
	/**
	 * @since 0.9.22
	 */
    const OPTION = 'aff_migrated_orphaned_product_variants_cleaner_timer_to_0.9.22';

	/**
	 * @since 0.9.22
	 * @var Orphaned_Product_Variants_Cleaner_Timer
	 */
	private $orphaned_product_variants_cleaner_timer;

	/**
	 * @since 0.9.22
	 * @param Orphaned_Product_Variants_Cleaner_Timer $orphaned_product_variants_cleaner_timer
	 */
	public function __construct(Orphaned_Product_Variants_Cleaner_Timer $orphaned_product_variants_cleaner_timer)
	{
		$this->orphaned_product_variants_cleaner_timer = $orphaned_product_variants_cleaner_timer;
	}

	/**
     * @since 0.9.22
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.9.22' && get_option(self::OPTION) != 'yes') {
	        $this->orphaned_product_variants_cleaner_timer->activate();

            update_option(self::OPTION, 'yes');
       }
    }
}
