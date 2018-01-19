<?php
namespace Affilicious\Common\Listener;

use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Drop_Tables_Listener
{
	/**
	 * This listener is called when the tables from an existing blog are deleted.
	 *
	 * @since 0.9.20
	 * @hook wpmu_drop_tables
	 * @param array $tables
	 * @param int $blog_id The newly created ID of the blog in the network.
	 * @return array
	 */
	public function listen(array $tables, $blog_id)
	{
		$tables['aff_logs'] = Logs_Table_Creator::get_table_name(true, $blog_id);

		return $tables;
	}
}
