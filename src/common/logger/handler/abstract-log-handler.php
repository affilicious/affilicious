<?php
namespace Affilicious\Common\Logger\Handler;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Log_Handler implements Handler_Interface
{
    /**
     * Get the log level key for the code.
     *
     * @since 0.9.18
     * @param int $level The level of the log message as in RFC 5424.
     * @return string The key of the level. One of: 'DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'
     */
    protected function get_log_level_key($level)
    {
        Assert_Helper::is_integer($level, __METHOD__, 'Expected the record to be an integer indication the log level as in RFC 5424. Got: %s', '0.9.18');

        $key = array_search($level, Logger::$levels, true);

        return $key;
    }
}
