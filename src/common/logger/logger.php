<?php
namespace Affilicious\Common\Logger;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Handler\Handler_Interface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * Support RFC 5424 as described in http://tools.ietf.org/html/rfc5424.
 * @since 0.9.11
 */
class Logger
{
	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_DEBUG = 100;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_INFO = 200;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_NOTICE = 250;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_WARNING = 300;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_ERROR = 400;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_CRITICAL = 500;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_ALERT = 550;

	/**
	 * @since 0.9.11
	 * @var int
	 */
	const LEVEL_EMERGENCY = 600;

	/**
	 * @since 0.9.11
	 * @var array
	 */
	public static $levels = [
		'DEBUG' => self::LEVEL_DEBUG,
		'INFO' => self::LEVEL_INFO,
		'NOTICE' => self::LEVEL_NOTICE,
		'WARNING' => self::LEVEL_WARNING,
		'ERROR' => self::LEVEL_ERROR,
		'CRITICAL' => self::LEVEL_CRITICAL,
		'ALERT' => self::LEVEL_ALERT,
		'EMERGENCY' => self::LEVEL_EMERGENCY,
	];

	/**
	 * @var Handler_Interface[]
	 * @since 0.9.11
	 */
	protected $handlers = [];

    /**
     * Create the log record.
     *
     * @since 0.9.18
     * @param string $message The message for the log.
     * @param string|int $level The level of the log message as in RFC 5424 or the level code directly.
     * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
     * @param null $created_at The creation date of the log.
     * @return string The record is an entry containing the message, level, $context and creation date in a standardized way.
     */
	public static function create_record($message, $level = self::LEVEL_DEBUG, $context = 'Affilicious', $created_at = null)
    {
        $created_at = !empty($created_at) ? $created_at : current_time('mysql', 1);
        $key = is_int($level) ? array_search($level, self::$levels, true) : $level;
        $record = '[' . $created_at . '] ' . $context . '.' . $key . ': ' . $message;

        return $record;
    }

	/**
	 * Add a message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param int $level The level of the log message as in RFC 5424.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function log($message, $level = self::LEVEL_DEBUG, $context = 'Affilicious')
	{
		Assert_Helper::is_string_not_empty($message, __METHOD__, 'Expected message to be a non empty string. Got: %s', '0.9.11');
		Assert_Helper::one_of($level, self::$levels, __METHOD__, 'Expected level to be one of %s. Got: %s', '0.9.11');
		Assert_Helper::is_string_not_empty($context, __METHOD__, 'Expected context to be a non empty string. Got: %s', '0.9.11');

		$created_at = current_time('mysql', 1);
		foreach($this->handlers as $handler) {
			$handler->handle($message, $level, $context, $created_at);
		}
	}

	/**
	 * Add a debug message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function debug($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_DEBUG, $context);
	}

	/**
	 * Add an info message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function info($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_INFO, $context);
	}

	/**
	 * Add a notice message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function notice($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_NOTICE, $context);
	}

	/**
	 * Add a warning message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function warning($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_WARNING, $context);
	}

	/**
	 * Add an error message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function error($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_ERROR, $context);
	}

	/**
	 * Add a critical message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function critical($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_CRITICAL, $context);
	}

	/**
	 * Add an alert message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function alert($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_ALERT, $context);
	}

	/**
	 * Add an emergency message to the log.
	 *
	 * @since 0.9.11
	 * @param string $message The message for the log.
	 * @param string $context The context of the log message e.g. your plugin or theme name. Default: Affilicious
	 */
	public function emergency($message, $context = 'Affilicious')
	{
		$this->log($message, self::LEVEL_EMERGENCY, $context);
	}

	/**
	 * Add a new handler to the logger.
	 *
	 * @since 0.9.11
	 * @param Handler_Interface $handler
	 */
	public function add_handler(Handler_Interface $handler)
	{
		$this->handlers[$handler->get_name()] = $handler;
	}

	/**
	 * Remove an existing handler from the logger.
	 *
	 * @since 0.9.11
	 * @param Handler_Interface $handler
	 */
	public function remove_handler(Handler_Interface $handler)
	{
		unset($this->handlers[$handler->get_name()]);
	}

	/**
	 * Get all existing handlers.
	 *
	 * @since 0.9.11
	 * @return Handler_Interface[]
	 */
	public function get_handlers()
	{
		return array_values($this->handlers);
	}
}
