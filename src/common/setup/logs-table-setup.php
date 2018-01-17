<?php
namespace Affilicious\Common\Setup;

use Affilicious\Common\Helper\Network_Helper;
use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Logs_Table_Setup
{
	/**
	 * @var Logs_Table_Creator
	 */
	protected $logs_table_creator;

	/**
	 * @since 0.9.19
	 * @param Logs_Table_Creator $logs_table_creator
	 */
	public function __construct(Logs_Table_Creator $logs_table_creator)
	{
		$this->logs_table_creator = $logs_table_creator;
	}

	/**
	 * Initialize the logs table used to store the produced logs.
	 *
	 * @since 0.9.18
	 * @param bool $network_wide
	 */
    public function init($network_wide = false)
    {
	    Network_Helper::for_each_blog(function() {
		    $this->logs_table_creator->create();
	    }, $network_wide);
    }
}
