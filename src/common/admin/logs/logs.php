<?php
namespace Affilicious\Common\Admin\Logs;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Logger;
use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.18
 */
class Logs
{
	/**
	 * @since 0.9.18
	 * @var string
	 */
	const NO_LIMIT = -1;

	/**
	 * Generate the logs to make the support easier.
	 *
	 * @since 0.9.18
	 * @param int $limit Optional. The limit of log records to show. Default: -1 for limitless.
	 * @return array
	 */
    public function generate($limit = self::NO_LIMIT)
    {
        global $wpdb;

        $table_name = Logs_Table_Creator::get_table_name();

        if($limit != self::NO_LIMIT) {
	        $query = "SELECT * FROM (SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT {$limit}) sub ORDER BY created_at ASC";
        } else {
	        $query = "SELECT * FROM (SELECT * FROM {$table_name} ORDER BY created_at DESC) sub ORDER BY created_at ASC";
        }

        $logs = $wpdb->get_results($query, ARRAY_A);
        if(empty($logs)) {
            return [];
        }

        // Remove the ID from the logs.
        array_walk($logs, function($log) {
            unset($log['id']);
        });

        $logs = apply_filters('aff_common_admin_logs_generate', $logs);
        Assert_Helper::is_array($logs, __METHOD__, 'Expected the logs to be an array. Got: %s', '0.9.18');

        return $logs;
    }

	/**
	 * Stringify the logs to make the support easier.
	 *
	 * @since 0.9.18
	 * @param int $limit Optional. The limit of log records to show. Default: -1 for limitless.
	 * @param bool $nl2br Convert the new line into <br>.
	 * @return string
	 */
    public function stringify($limit = self::NO_LIMIT, $nl2br = false)
    {
        $logs = $this->generate($limit);

        $result = '';

        foreach ($logs as $log) {
            $record = Logger::create_record($log['message'], $log['level'], $log['context'], $log['created_at']);
            $result .= "{$record}\n";
        }

        if($nl2br) {
            $result = nl2br($result);
        }

        $result = apply_filters('aff_common_admin_logs_stringify', $result);
        Assert_Helper::is_string($result, __METHOD__, 'Expected the logs to be a string. Got: %s', '0.9.18');

        return $result;
    }

	/**
	 * Render the logs to make the support easier.
	 *
	 * @since 0.9.9
	 * @param int $limit Optional. The limit of log records to show. Default: -1 for limitless.
	 * @param bool $escape Whether to escape the output or not.
	 */
    public function render($limit = self::NO_LIMIT, $escape = true)
    {
        $logs = $this->stringify($limit, true);

        $logs = apply_filters('aff_common_admin_logs_render', $logs);
        Assert_Helper::is_string($logs, __METHOD__, 'Expected the logs to be a string. Got: %s', '0.9.18');

        if($escape) {
            $logs = esc_html($logs);
        }

        echo $logs;
    }
}
