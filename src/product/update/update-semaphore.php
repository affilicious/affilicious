<?php
namespace Affilicious\Product\Update;

use Affilicious\Common\Helper\Network_Helper;
use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * The binary semaphore protects the update process to be called multiple times in parallel causing race conditions.
 */
final class Update_Semaphore
{
	const COUNTER_HOURLY_OPTION = 'affilicious_update_semaphore_counter_hourly';
	const LAST_ACQUIRE_TIME_HOURLY_OPTION = 'affilicious_update_semaphore_last_acquire_time_hourly';

	const COUNTER_TWICE_DAILY_OPTION = 'affilicious_update_semaphore_counter_twicedaily';
	const LAST_ACQUIRE_TIME_TWICE_DAILY_OPTION = 'affilicious_update_semaphore_last_acquire_time_twicedaily';

	const COUNTER_DAILY_OPTION = 'affilicious_update_semaphore_counter_daily';
	const LAST_ACQUIRE_TIME_DAILY_OPTION = 'affilicious_update_semaphore_last_acquire_time_daily';

	public static $counter_options = [
		'hourly' => self::COUNTER_HOURLY_OPTION,
		'twicedaily' => self::COUNTER_TWICE_DAILY_OPTION,
		'daily' => self::COUNTER_DAILY_OPTION,
	];

	public static $last_acquire_time_options = [
		'hourly' => self::LAST_ACQUIRE_TIME_HOURLY_OPTION,
		'twicedaily' => self::LAST_ACQUIRE_TIME_TWICE_DAILY_OPTION,
		'daily' => self::LAST_ACQUIRE_TIME_DAILY_OPTION,
	];

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @since 0.9.11
	 * @param Logger $logger
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Acquire the binary semaphore, which is nearly the same as locking.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	public function acquire($update_interval)
	{
		// Check if the semaphore is available.
		$result = $this->decrement($update_interval) || $this->check_stuck($update_interval);
		if(!$result) {
			$this->logger->debug(sprintf('Skipped to acquire update semaphore. (%s)', $update_interval));

			return false;
		}

		// Set the last acquire time to now.
		$result = $this->update_last_acquire_time($update_interval);
		if(!$result) {
			return false;
		}

		// Everything is ok.
		$this->logger->info(sprintf('Successfully acquired the update semaphore. (%s)', $update_interval));

		return true;
	}

	/**
	 * Release the binary semaphore, which is nearly the same as unlocking.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	public function release($update_interval)
	{
		// The semaphore is available again.
		$result = $this->increment($update_interval);
		if(!$result) {
			$this->logger->error(sprintf('Failed to release the update semaphore. (%s)', $update_interval));

			return false;
		}

		// Everything is ok.
		$this->logger->info(sprintf('Successfully released the update semaphore. (%s)', $update_interval));

		return true;
	}

	/**
	 * Install the semaphore with all options to make the future operations more atomic.
	 *
	 * @since 0.9.11
	 * @param bool $network_wide Optional. Install the semaphore for the complete multisite. Default: false.
	 */
	public function install($network_wide = false)
	{
		Network_Helper::for_each_blog(function() {
			update_option(self::COUNTER_HOURLY_OPTION, '1', false);
			update_option(self::LAST_ACQUIRE_TIME_HOURLY_OPTION, current_time('mysql', 1), false);

			update_option(self::COUNTER_TWICE_DAILY_OPTION, '1', false);
			update_option(self::LAST_ACQUIRE_TIME_TWICE_DAILY_OPTION, current_time('mysql', 1), false);

			update_option(self::COUNTER_DAILY_OPTION, '1', false);
			update_option(self::LAST_ACQUIRE_TIME_DAILY_OPTION, current_time('mysql', 1), false);
		}, $network_wide);
	}

	/**
	 * Uninstall the semaphore with all options in the database.
	 *
	 * @since 0.9.11
	 * @param bool $network_wide bool $network_wide Optional. Install the semaphore for the complete multisite. Default: false.
	 */
	public function uninstall($network_wide = false)
	{
		Network_Helper::for_each_blog(function() {
			delete_option(self::COUNTER_HOURLY_OPTION);
			delete_option(self::LAST_ACQUIRE_TIME_HOURLY_OPTION);

			delete_option(self::COUNTER_TWICE_DAILY_OPTION);
			delete_option(self::LAST_ACQUIRE_TIME_TWICE_DAILY_OPTION);

			delete_option(self::COUNTER_DAILY_OPTION);
			delete_option(self::LAST_ACQUIRE_TIME_DAILY_OPTION);
		}, $network_wide);
	}

	/**
	 * Attempts to jiggle the stuck lock loose.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the stuck was removed or not.
	 */
	private function check_stuck($update_interval)
	{
		global $wpdb;

		// Check if the semaphore is stuck. Try to reset the last acquire time if 1 hour has passed already.
		$current_time = current_time('mysql', 1);
		$unlock_time = gmdate('Y-m-d H:i:s', time() - 60 * 60); // 1 hour
		$last_acquire_time_option = self::$last_acquire_time_options[$update_interval];
		$query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value <= %s", $current_time, $last_acquire_time_option, $unlock_time);
		$result = $wpdb->query($query);

		// Something went wrong...
		if ($result != 1) {
			$this->logger->error(sprintf("Failed to update the update semaphore last acquire time to %s. It's still stuck. (%s)", $current_time, $update_interval));

			return false;
		}

		// Everything is ok.
		$this->reset($update_interval);
		$this->logger->debug(sprintf('The update semaphore was stuck. Set lock time to %s. (%s)', $current_time, $update_interval));

		return true;
	}

	/**
	 * Update the last acquire time to now.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	private function update_last_acquire_time($update_interval)
	{
		global $wpdb;

		// Set the last acquire time to now.
		$current_time = current_time('mysql', 1);
		$last_acquire_time_option = self::$last_acquire_time_options[$update_interval];
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s", $current_time, $last_acquire_time_option);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			$this->logger->alert(sprintf('Failed to update the update semaphore last acquire time to %s. (%s)', $current_time, $update_interval));

			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Updated the update semaphore last acquire time to %s. (%s)', $current_time, $update_interval));

		return true;
	}

	/**
	 * Reset the binary semaphore.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	private function reset($update_interval)
	{
		global $wpdb;

        // Reset the semaphore counter (Test And Set operation)...
		$counter_option = self::$counter_options[$update_interval];
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '1' WHERE option_name = %s", $counter_option);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			$this->logger->error(sprintf('Failed to reset the update semaphore counter to 1. (%s)', $update_interval));

			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Reset the update semaphore counter to 1. (%s)', $update_interval));

		return true;
	}

	/**
	 * Increment the binary semaphore.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	private function increment($update_interval)
	{
		global $wpdb;

		// Try to increment the semaphore (Test And Set operation)...
		$counter_option = self::$counter_options[$update_interval];
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '1' WHERE option_name = %s AND option_value = '0'", $counter_option);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Incremented the update semaphore counter to 1. (%s)', $update_interval));

		return true;
	}

	/**
	 * Decrement the binary semaphore.
	 *
	 * @since 0.9.11
	 * @param string $update_interval The current cron job update interval like "hourly", "twicedaily" or "daily".
	 * @return bool Whether the operation was successful or not.
	 */
	private function decrement($update_interval)
	{
		global $wpdb;

		// Try to decrement the semaphore (Test And Set operation)...
		$counter_option = self::$counter_options[$update_interval];
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '0' WHERE option_name = %s AND option_value = '1'", $counter_option);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Decremented the update semaphore counter to 0. (%s)', $update_interval));

		return true;
	}
}
