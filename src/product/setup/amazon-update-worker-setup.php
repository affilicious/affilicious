<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Update\Worker\Amazon_Update_Worker;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker_Setup
{
    /**
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.9
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Product_Repository_Interface $product_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    ) {
        $this->product_repository = $product_repository;
        $this->shop_template_repository = $shop_template_repository;
        $this->provider_repository = $provider_repository;
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
        $amazon_worker = new Amazon_Update_Worker(
            $this->product_repository,
            $this->shop_template_repository,
            $this->provider_repository
        );

        $update_workers[$amazon_worker->get_name()] = $amazon_worker;

        return $update_workers;
    }
}
