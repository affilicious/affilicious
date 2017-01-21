<?php
namespace Affilicious\Product\Setup;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Update\Worker\Amazon\Amazon_Update_Worker;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker_Setup
{
    /**
     * Init the amazon update worker for regularly updated products.
     *
     * @since 0.7
     * @param Update_Worker_Interface[] $update_workers
     * @return Update_Worker_Interface[]
     */
    public function init($update_workers)
    {
        $amazon_worker = new Amazon_Update_Worker(new Slug('amazon'));
        $update_workers['amazon'] = $amazon_worker;

        return $update_workers;
    }
}
