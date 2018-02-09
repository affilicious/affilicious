<?php
namespace Affilicious\Product\Cleaner;

use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Orphaned_Product_Variants_Cleaner
{
	/**
	 * @since 0.9.22
	 * @var Logger
	 */
	private $logger;

	/**
	 * @since 0.9.22
	 * @param Logger $logger
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Clean up the orphaned product variants (product variants with no parent product).
	 *
	 * @since 0.9.22
	 */
	public function clean_up()
	{
		global $wpdb;

		$this->logger->debug('Try to clean up the orphaned product variants.');

		do_action('aff_product_cleaner_orphaned_product_variants_before_clean_up');

		$number_of_deleted_variants = 0;

		$product_variants = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'aff_product' AND post_parent > 0");
		if(!empty($product_variants)) {
			foreach ($product_variants as $product_variant) {
				$parent_complex_product = get_post($product_variant->post_parent);
				if(empty($parent_complex_product)) {
					$result = wp_delete_post($product_variant->ID);
					if(!empty($result)) {
						$number_of_deleted_variants++;
					} else {
						$this->logger->error(sprintf('Failed to clean up orphaned product variants #%d (%s).', $product_variant->ID, $product_variant->post_title));
					}
				}
			}
		}

		do_action('aff_product_cleaner_orphaned_product_variants_after_clean_up');

		// Everything is ok.
		if($number_of_deleted_variants > 0) {
			$this->logger->debug(sprintf('Successfully cleaned up %d orphaned product variants.', $number_of_deleted_variants));
		} else {
			$this->logger->debug('No orphaned product variants have been cleaned up.');
		}
	}
}
