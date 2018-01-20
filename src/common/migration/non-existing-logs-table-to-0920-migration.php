<?php
namespace Affilicious\Common\Migration;

use Affilicious\Common\Table_Creator\Logs_Table_Creator;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Non_Existing_Logs_Table_To_0920_Migration
{
    const OPTION = 'aff_migrated_non_existing_logs_table_to_0.9.20';

	/**
	 * @since 0.9.20
	 * @var Logs_Table_Creator
	 */
	protected $logs_table_creator;

	/**
	 * @since 0.9.20
	 * @param Logs_Table_Creator $logs_table_creator
	 */
	public function __construct(Logs_Table_Creator $logs_table_creator)
	{
		$this->logs_table_creator = $logs_table_creator;
	}

	/**
     * @since 0.9.20
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.9.20' && get_option(self::OPTION) != 'yes') {
	        $this->logs_table_creator->create();

            update_option(self::OPTION, 'yes');
       }
    }
}
