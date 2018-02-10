<?php
namespace Affilicious\Product\Cleaner;

use Affilicious\Common\Timer\Abstract_Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Orphaned_Product_Variants_Cleaner_Timer extends Abstract_Timer
{
	/**
	 * @since 0.9.22
	 * @var Orphaned_Product_Variants_Cleaner
	 */
	private $orphaned_product_variants_cleaner;

	/**
	 * @since 0.9.22
	 * @param Orphaned_Product_Variants_Cleaner $orphaned_product_variants_cleaner
	 */
	public function __construct(Orphaned_Product_Variants_Cleaner $orphaned_product_variants_cleaner)
	{
		$this->orphaned_product_variants_cleaner = $orphaned_product_variants_cleaner;
	}

	/**
	 * @inheritdoc
	 * @since 0.9.22
	 */
	public function activate($network_wide = false)
	{
		$this->add_scheduled_action('aff_product_cleaner_orphaned_product_variants_clean_up_daily', 'daily', $network_wide);
	}

	/**
	 * @inheritdoc
	 * @since 0.9.22
	 */
	public function deactivate($network_wide = false)
	{
		$this->remove_scheduled_action('aff_product_cleaner_orphaned_product_variants_clean_up_daily', $network_wide);
	}

	/**
	 * Run the orphaned product variants cleaner daily as cron jobs.
	 *
	 * @hook aff_product_cleaner_orphaned_product_variants_clean_up_daily
	 * @since 0.9.22
	 */
	public function clean_up_daily()
	{
		$this->orphaned_product_variants_cleaner->clean_up();
	}
}
