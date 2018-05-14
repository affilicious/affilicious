<?php
namespace Affilicious\Common\Admin\Action;

use Affilicious\Common\Admin\Logs\Logs;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.18
 */
class Download_Logs_Action
{
	/**
	 * @since 0.9.18
	 * @var string
	 */
	const LOGS_LIMIT = 1000;

	/**
	 * @since 0.9.18
	 * @var string
	 */
    const ACTION = 'aff_download_logs';

	/**
	 * @since 0.9.18
	 * @var string
	 */
    const FILENAME = 'affilicious-logs.txt';

    /**
     * @since 0.9.18
     * @var Logs
     */
    protected $logs;

    /**
     * @since 0.9.18
     * @param Logs $logs
     */
    public function __construct(Logs $logs)
    {
        $this->logs = $logs;
    }

    /**
     * Handle the logs download.
     *
     * @hook admin_action_aff_download_logs
     * @since 0.9.18
     */
    public function handle()
    {
        $action = filter_input(INPUT_GET, 'action');
        $nonce  = filter_input(INPUT_GET, 'nonce');

        if ($action === self::ACTION && wp_verify_nonce($nonce, self::ACTION)) {
            $this->download_logs();
        }

        die();
    }

    /**
     * Create the logs txt.
     *
     * @since 0.9.18
     */
    protected function download_logs()
    {
        header('Content-type: text/plain');
        header(sprintf('Content-Disposition: attachment; filename="%s"', self::FILENAME));

        $logs = $this->logs->stringify(self::LOGS_LIMIT);
        echo $logs;
    }
}
