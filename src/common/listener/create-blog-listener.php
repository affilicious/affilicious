<?php
namespace Affilicious\Common\Listener;

use Affilicious\Common\Helper\Network_Helper;
use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Create_Blog_Listener
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
	 * This listener is called when a new blog is created in a multisite.
	 *
	 * @since 0.9.19
	 * @hook wpmu_new_blog
	 * @param int $blog_id The newly created ID of the blog in the network.
	 */
	public function listen($blog_id)
	{
		$this->create_logs_table($blog_id);
	}

	/**
	 * Create the logs table for the newly created blog.
	 *
	 * @since 0.9.19
	 * @param int $blog_id The newly created ID of the blog in the network.
	 */
	protected function create_logs_table($blog_id)
	{
		if (is_plugin_active_for_network('affilicious/affilicious.php')) {
			Network_Helper::for_blog($blog_id, function() {
				$this->logs_table_creator->create();
			});
		}
	}
}
