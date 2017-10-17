<?php
namespace Affilicious\Common\Logger\Handler;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface Handler_Interface
{
	/**
	 * Get the handler name.
	 *
	 * @since 0.9.11
	 * @return string
	 */
	public function get_name();

	/**
	 * Handle the log record.
	 *
	 * @since 0.9.11
	 * @param string $record
	 * @return bool
	 */
	public function handle($record);
}
