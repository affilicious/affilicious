<?php
namespace Affilicious\Common\Logger\Handler;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Error_Log_Handler extends Abstract_Log_Handler
{
	/**
	 * @var string|null
	 */
	protected $file_path;

	/**
	 * Sets the log pile path.
	 *
     * @since 0.9.11
	 * @param string|null $file_path
	 */
	public function __construct($file_path = null)
	{
		if($this->create_file($file_path)) {
			$this->file_path = $file_path;
		}
	}

	/**
	 * @inheritdoc
	 * @since 0.9.11
	 */
	public function get_name()
	{
		return 'error_log';
	}

	/**
	 * @inheritdoc
	 * @since 0.9.11
	 */
	public function handle($message, $level, $context, $created_at)
	{
        Assert_Helper::is_string_not_empty($message, __METHOD__, 'Expected the message to be a non empty string. Got: %s', '0.9.18');
        Assert_Helper::is_integer($level, __METHOD__, 'Expected the level to be an integer indication the log level as in RFC 5424. Got: %s', '0.9.18');
        Assert_Helper::is_string_not_empty($context, __METHOD__, 'Expected the context to be a non empty string. Got: %s', '0.9.18');
        Assert_Helper::is_string_not_empty($created_at, __METHOD__, 'Expected the creation date to be a non empty string. Got: %s', '0.9.18');

        $record = Logger::create_record($message, $level, $context, $created_at);
		if ($this->file_path !== null && is_writable($this->file_path)) {
			error_log($record . "\n", 3, $this->file_path);
		} else {
			error_log($record);
		}
	}

	/**
     * Create a writable logs file.
     *
	 * @since 0.9.11
	 * @param string $file_path
	 * @return bool
	 */
	protected function create_file($file_path)
	{
		if (!is_writable(dirname($file_path))) {
			return false;
		}

		try {
			$fh = fopen($file_path, 'a');
			fclose($fh);
		}
		catch (\Exception $e) {
			return false;
		}

		return true;
	}
}
