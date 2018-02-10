<?php
namespace Affilicious\Common\Cleaner;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Logger;
use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.22
 */
final class Logs_Cleaner
{
	/**
	 * By default, only 10.000 log records are allowed.
	 *
	 * @since 0.9.22
	 */
	const DEFAULT_LIMIT = 10000;

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
	 * Clean the logs until a limit is reached.
	 *
	 * @since 0.9.22
	 */
	public function clean_up()
	{
		global $wpdb;

		// Find out the log limit and tableto clear.
		$logs_table = Logs_Table_Creator::get_table_name();
		$limit = self::DEFAULT_LIMIT;

		$limit = apply_filters('aff_common_cleaner_logs_limit', $limit, $logs_table);
		Assert_Helper::is_integer($limit, __METHOD__, 'The logs cleaner limit must be an integer. Got: %s', '0.9.22');

		$this->logger->debug(sprintf('Try to clean up to the limit %d of log records in the database table "%s".', $limit, $logs_table));

		// We need to subtract 1 logs to finally keep the exact amount of log records in the database
		if($limit > 0) {
			$limit--;
		}

		// Try to clean up the given amount of log records and check for errors.
		do_action('aff_common_cleaner_logs_before_clean_up', $limit, $logs_table);

		$number_of_records = $wpdb->query("DELETE FROM {$logs_table} WHERE id NOT IN (SELECT id FROM (SELECT id FROM {$logs_table} ORDER BY id DESC LIMIT {$limit}) temp);");
		if($number_of_records === false) {
			$this->logger->error(sprintf('Failed to clean up %d log records from the database table %s.', $limit, $logs_table));
			return;
		}

		do_action('aff_common_cleaner_logs_after_clean_up', $limit, $logs_table);

		// Everything is ok.
		if($number_of_records > 0) {
			$this->logger->debug(sprintf('Successfully cleaned up %d log records from the database table "%s".', $number_of_records, $logs_table));
		} else {
			$this->logger->debug(sprintf('No log records have been cleaned up from the database table "%s".', $logs_table));
		}
	}
}
