<?php
namespace Affilicious\Common\Logger\Handler;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Setup\Logs_Table_Setup;

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

		// Check if there is a table for the logs.
		$table_name = Logs_Table_Setup::get_table_name();
		if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            return;
        }

        // Store the log record into the table.
        $level = $this->get_log_level_key($level);
        $wpdb->insert($table_name, [
            'message' => $message,
            'level' => $level,
            'context' => $context,
            'created_at' => $created_at,
        ]);
	}
}
