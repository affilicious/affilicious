<?php
namespace Affilicious\Product\Update;

use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * The binary semaphore protects the update process to be called multiple times in parallel.
 */
final class Update_Semaphore
{
	const LOCK_OPTION = 'affilicious_update_semaphore_lock';
	const COUNTER_OPTION = 'affilicious_update_semaphore_counter';
	const LAST_ACQUIRE_TIME_OPTION = 'affilicious_update_semaphore_last_acquire_time';

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var bool
	 */
	private $stuck_broke = false;

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
		// Lock the acquire operation.
		$result = $this->lock() || $this->check_stuck();
		if(!$result) {
			$this->logger->info('Skipped to acquire the semaphore.');

			return false;
		}

		// Check if the semaphore is available.
		$result = $this->decrement() || $this->check_stuck();
		if(!$result) {
			$this->logger->info('Skipped to acquire the semaphore.');

			return false;
		}

		// Set the last acquire time to now.
		$result = $this->update_last_acquire_time();
		if(!$result) {
			return false;
		}

		// Unlock the acquire operation.
		$this->unlock();

		// Everything is ok.
		$this->logger->info('Successfully acquired the semaphore.');

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
			$this->logger->error('Failed to release the semaphore.');

			return false;
		}

		// Everything is ok.
		$this->logger->info('Successfully released the semaphore.');

		return true;
	}

	/**
	 * Install the semaphore with all options to make the future operations more atomic.
	 *
	 * @since 0.9.11
	 */
	public function install()
	{
		update_option(self::LOCK_OPTION, '0', false);
		update_option(self::COUNTER_OPTION, '1', false);
		update_option(self::LAST_ACQUIRE_TIME_OPTION, current_time('mysql', 1), false);
	}

	/**
	 * Uninstall the semaphore with all options in the database.
	 *
	 * @since 0.9.11
	 */
	public function uninstall()
	{
		delete_option(self::LOCK_OPTION);
		delete_option(self::COUNTER_OPTION);
		delete_option(self::LAST_ACQUIRE_TIME_OPTION);
	}

	/**
	 * Lock the semaphore to make the acquire operation atomic.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function lock()
	{
		global $wpdb;

		// Attempt to set the lock
		$result = $wpdb->query("
			 UPDATE $wpdb->options
			 SET option_value = '1'
			 WHERE option_name = '" . self::LOCK_OPTION . "'
			 AND option_value = '0'
		");

		// Something went wrong...
		if ($result != 1) {
			$this->logger->error('Failed to lock the update semaphore.');

			return false;
		}

		// Everything is ok.
		$this->logger->debug('Successfully locked the update semaphore.');

		return true;
	}

	/**
	 * Unlock the semaphore to make the acquire operation atomic.
	 *
	 * @since 0.9.11
	 * @return bool Whether the operation was successful or not.
	 */
	private function unlock()
	{
		global $wpdb;

		// Attempt to set the lock
		$result = $wpdb->query("
			 UPDATE $wpdb->options
			 SET option_value = '0'
			 WHERE option_name = '" . self::LOCK_OPTION . "'
			 AND option_value = '1'
		");

		// Something went wrong...
		if ($result != 1) {
			$this->logger->error('Failed to unlock the update semaphore.');

			return false;
		}

		// Everything is ok.
		$this->logger->debug('Successfully unlocked the update semaphore.');

		return true;
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

		// Check to see if the stuck is already broken.
		if ($this->stuck_broke) {
			return true;
		}

		// Try to reset the last acquire time.
		$current_time = current_time('mysql', 1);
		$unlock_time = gmdate('Y-m-d H:i:s', time() - 30 * 60);

		$result = $wpdb->query($wpdb->prepare("
		    UPDATE $wpdb->options
		    SET option_value = %s
			WHERE option_name = '" . self::LAST_ACQUIRE_TIME_OPTION . "'
			AND option_value <= %s
		", $current_time, $unlock_time));

		// The last acquire time was reseted successfully.
		if ($result != 1) {
			$this->logger->error(sprintf('Update semaphore is still stuck. Failed to set lock time to %s', $current_time));

			return false;
		}

		// Everything is ok.
		$this->reset();
		$this->unlock();
		$this->stuck_broke = true;
		$this->logger->debug(sprintf('Update semaphore was stuck. Set lock time to %s', $current_time));

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
		$result = $wpdb->query($wpdb->prepare("
			UPDATE $wpdb->options
			SET option_value = %s
			WHERE option_name = '" . self::LAST_ACQUIRE_TIME_OPTION . "'
		", $current_time));

		// Something went wrong...
		if($result != 1) {
			$this->logger->error(sprintf('Failed to update the update semaphore last acquire time to %s', $current_time));

			return false;
		}

		// Everything is ok.
		$this->logger->debug(sprintf('Updated the update semaphore last acquire time to %s', $current_time));

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

		// Reset the semaphore counter.
		$result = $wpdb->query("
			UPDATE $wpdb->options
			SET option_value = '1'
			WHERE option_name = '" . self::COUNTER_OPTION . "'
		");

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

		// Try to increment the semaphore.
		$result = $wpdb->query("
			 UPDATE $wpdb->options
			 SET option_value = '1'
			 WHERE option_name = '" . self::COUNTER_OPTION . "'
			 AND option_value = '0'
		");

		// Something went wrong...
		if($result != 1) {
			$this->logger->error('Failed to incremented the update semaphore counter to 1.');

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

		// Try to decrement the semaphore.
		$result = $wpdb->query("
		   	 UPDATE $wpdb->options
			 SET option_value = '0'
			 WHERE option_name = '" . self::COUNTER_OPTION . "'
			 AND option_value = '1'
		");

		// Something went wrong...
		if($result != 1) {
			$this->logger->error('Failed to decremented the update semaphore counter to 0.');

			return false;
		}

		// Everything is ok.
		$this->logger->debug('Decremented the update semaphore counter to 0.');

		return true;
	}
}
