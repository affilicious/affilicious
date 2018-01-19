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
	const COUNTER_OPTION = 'affilicious_update_semaphore_counter';
	const LAST_ACQUIRE_TIME_OPTION = 'affilicious_update_semaphore_last_acquire_time';

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
	 * @return bool Whether the operation was successful or not.
	 */
	public function acquire()
	{
		// Check if the semaphore is available.
		$result = $this->decrement() || $this->check_stuck();
		if(!$result) {
			$this->logger->debug('Skipped to acquire update semaphore.');

			return false;
		}

		// Set the last acquire time to now.
		$result = $this->update_last_acquire_time();
		if(!$result) {
			return false;
		}

		// Everything is ok.
		$this->logger->debug('Successfully acquired the update semaphore.');

		return true;
	}

	/**
	 * Release the binary semaphore, which is nearly the same as unlocking.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	public function release()
	{
		// The semaphore is available again.
		$result = $this->increment();
		if(!$result) {
			$this->logger->error('Failed to release the update semaphore.');

			return false;
		}

		// Everything is ok.
		$this->logger->debug('Successfully released the update semaphore.');

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
			update_option(self::COUNTER_OPTION, '1', false);
			update_option(self::LAST_ACQUIRE_TIME_OPTION, current_time('mysql', 1), false);
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
			delete_option(self::COUNTER_OPTION);
			delete_option(self::LAST_ACQUIRE_TIME_OPTION);
		}, $network_wide);
	}

	/**
	 * Attempts to jiggle the stuck lock loose.
	 *
	 * @since 0.9.11
	 * @return bool Whether the stuck was removed or not.
	 */
	private function check_stuck()
	{
		global $wpdb;

		// Check if the semaphore is stuck. Try to reset the last acquire time if 3 hours have passed already.
		$current_time = current_time('mysql', 1);
		$unlock_time = gmdate('Y-m-d H:i:s', time() - 60 * 60 * 3); // 3 hours

		$query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value <= %s", $current_time, self::LAST_ACQUIRE_TIME_OPTION, $unlock_time);
		$result = $wpdb->query($query);

		// Something went wrong...
		if ($result != 1) {
			$this->logger->error(sprintf('Failed to update the update semaphore last acquire time to %s. It\'s still stuck.', $current_time));

			return false;
		}

		// Everything is ok.
		$this->reset();
		$this->logger->debug(sprintf('The update semaphore was stuck. Set lock time to %s', $current_time));

		return true;
	}

	/**
	 * Update the last acquire time to now.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function update_last_acquire_time()
	{
		global $wpdb;

		// Set the last acquire time to now.
		$current_time = current_time('mysql', 1);

        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s", $current_time, self::LAST_ACQUIRE_TIME_OPTION);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			$this->logger->error(sprintf('Failed to update the update semaphore last acquire time to %s.', $current_time));

			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Updated the update semaphore last acquire time to %s.', $current_time));

		return true;
	}

	/**
	 * Reset the binary semaphore.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function reset()
	{
		global $wpdb;

        // Reset the semaphore counter (Test And Set operation)...
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '1' WHERE option_name = %s", self::COUNTER_OPTION);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			$this->logger->error('Failed to reset the update semaphore counter to 1.');

			return false;
		}

		// Everything is ok.
		$this->logger->debug('Reset the update semaphore counter to 1.');

		return true;
	}

	/**
	 * Increment the binary semaphore.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function increment()
	{
		global $wpdb;

		// Try to increment the semaphore (Test And Set operation)...
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '1' WHERE option_name = %s AND option_value = '0'", self::COUNTER_OPTION);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			return false;
		}

		// Everything is ok.
		$this->logger->debug('Incremented the update semaphore counter to 1.');

		return true;
	}

	/**
	 * Decrement the binary semaphore.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function decrement()
	{
		global $wpdb;

		// Try to decrement the semaphore (Test And Set operation)...
        $query = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '0' WHERE option_name = %s AND option_value = '1'", self::COUNTER_OPTION);
		$result = $wpdb->query($query);

		// Something went wrong...
		if($result != 1) {
			return false;
		}

		// Everything is ok.
		$this->logger->debug('Decremented the update semaphore counter to 0.');

		return true;
	}
}
