<?php
namespace Affilicious\Common\Logger\Handler;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

interface Handler_Interface
{
	/**
	 * Get the logs handler name.
	 *
	 * @since 0.9.11
	 * @return string
	 */
	public function get_name();

    /**
     * Handle the log record.
     *
     * @since 0.9.18
     * @param string $record The record is an entry containing the message, level, $context and creation date in a standardized way.
     * @param string $message The message for the log.
     * @param string $level The level of the log message as in RFC 5424.
     * @param string $context The context of the log message e.g. your plugin or theme name.
     * @param string $created_at The creation date for the log.
     */
	public function handle($record, $message, $level, $context, $created_at);
}
