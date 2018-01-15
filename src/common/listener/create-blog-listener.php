<?php
namespace Affilicious\Common\Listener;

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
	 * @param $blog_id
	 * @param $user_id
	 * @param $domain
	 * @param $path
	 * @param $site_id
	 * @param $meta
	 */
	public function listen($blog_id, $user_id, $domain, $path, $site_id, $meta)
	{
		$this->create_logs_table($blog_id);
	}

	/**
	 * Create the logs table for the newly created blog.
	 *
	 * @since 0.9.18
	 * @param int $blog_id
	 */
	protected function create_logs_table($blog_id)
	{
		if (is_plugin_active_for_network('affilicious/affilicious.php')) {
			$this->logs_table_creator->create_for_multisite($blog_id);
		}
	}
}
