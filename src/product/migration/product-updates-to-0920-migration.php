<?php
namespace Affilicious\Product\Migration;

use Affilicious\Product\Update\Update_Semaphore;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Product_Updates_To_0920_Migration
{
    const OPTION = 'aff_migrated_product_updates_to_0.9.20';

	/**
	 * @since 0.9.20
	 * @var Update_Semaphore
	 */
	protected $update_semaphore;

	/**
	 * @since 0.9.20
	 * @param Update_Semaphore $update_semaphore
	 */
	public function __construct(Update_Semaphore $update_semaphore)
	{
		$this->update_semaphore = $update_semaphore;
	}

	/**
     * @since 0.9.20
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.9.20' && get_option(self::OPTION) != 'yes') {
	        $this->update_semaphore->install();

	        delete_option('affilicious_update_semaphore_counter');
	        delete_option('affilicious_update_semaphore_last_acquire_time');

            update_option(self::OPTION, 'yes');
       }
    }
}
