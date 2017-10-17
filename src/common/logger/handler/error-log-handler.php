<?php
namespace Affilicious\Common\Logger\Handler;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Error_Log_Handler implements Handler_Interface
{
	/**
	 * @var string|null
	 */
	protected $file_path;

	/**
	 * Sets the log pile path.
	 *
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
	public function handle($record)
	{
		Assert_Helper::is_string_not_empty($record, __METHOD__, 'Expected the record to be a non empty string. Got: %s', '0.9.11');

		if ($this->file_path !== null && is_writable($this->file_path)) {
			error_log($record . "\n", 3, $this->file_path);
		} else {
			error_log($record);
		}
	}

	/**
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
