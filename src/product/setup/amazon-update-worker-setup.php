<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Update\Worker\Update_Worker_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker_Setup
{
	/**
	 * @var Update_Worker_Interface
	 */
	protected $amazon_update_worker;

	/**
	 * @since 0.9.21
	 * @param Update_Worker_Interface $amazon_update_worker
	 */
    public function __construct(Update_Worker_Interface $amazon_update_worker)
    {
	    $this->amazon_update_worker = $amazon_update_worker;
    }

    /**
     * Init the amazon update worker for regularly updated products.
     *
     * @filter aff_product_update_worker_init
     * @since 0.7
     * @param Update_Worker_Interface[] $update_workers
     * @return Update_Worker_Interface[]
     */
    public function init($update_workers)
    {
        $update_workers[$this->amazon_update_worker->get_name()] = $this->amazon_update_worker;

        return $update_workers;
    }
}
