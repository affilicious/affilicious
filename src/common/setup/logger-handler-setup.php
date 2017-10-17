<?php
namespace Affilicious\Common\Setup;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Logger\Handler\Error_Log_Handler;
use Affilicious\Common\Logger\Handler\Handler_Interface;
use Affilicious\Common\Logger\Logger;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Logger_Handler_Setup
{
	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @since 0.9.11
	 * @param Logger $logger
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
     * Add the logger handlers to the logger.
     *
     * @hook init
     * @since 0.9.11
     */
    public function init()
    {
	    do_action('aff_logger_handler_before_init');

	    $logger_handlers = apply_filters('aff_logger_handler_init', $this->get_default_handlers());
	    Assert_Helper::is_array($logger_handlers, __METHOD__, 'Expected the logger handlers to be an array. Got: %s', '0.9.11');

	    foreach ($logger_handlers as $logger_handler) {
		    $this->logger->add_handler($logger_handler);
	    }

	    do_action('aff_logger_handler_after_init', $logger_handlers);
    }

	/**
	 * Get the default logger handlers.
	 *
	 * @since 0.9.11
	 * @return Handler_Interface[]
	 */
    private function get_default_handlers()
    {
    	return [
    		new Error_Log_Handler(AFFILICIOUS_ROOT_PATH . 'tmp/system.log')
	    ];
    }
}
