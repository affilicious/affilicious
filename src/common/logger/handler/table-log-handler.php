<?php
namespace Affilicious\Common\Logger\Handler;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Table_Log_Handler extends Abstract_Log_Handler
{
	/**
	 * @inheritdoc
	 * @since 0.9.18
	 */
	public function get_name()
	{
		return 'table_log';
	}

	/**
	 * @inheritdoc
	 * @since 0.9.18
	 */
	public function handle($record, $message, $level, $context, $created_at)
	{
	    global $wpdb;

		Assert_Helper::is_string_not_empty($record, __METHOD__, 'Expected the record to be a non empty string. Got: %s', '0.9.18');
		Assert_Helper::is_string_not_empty($message, __METHOD__, 'Expected the message to be a non empty string. Got: %s', '0.9.18');
		Assert_Helper::is_integer($level, __METHOD__, 'Expected the level to be an integer indication the log level as in RFC 5424. Got: %s', '0.9.18');
		Assert_Helper::is_string_not_empty($context, __METHOD__, 'Expected the context to be a non empty string. Got: %s', '0.9.18');
		Assert_Helper::is_string_not_empty($created_at, __METHOD__, 'Expected the creation date to be a non empty string. Got: %s', '0.9.18');

		$level = $this->get_log_level_key($level);

		$wpdb->insert($wpdb->prefix . 'aff_logs', [
		    'message' => $message,
            'level' => $level,
            'context' => $context,
            'created_at' => $created_at,
        ]);
	}
}
