<?php
namespace Affilicious\Common\Admin\Setup;

use Affilicious\Common\Admin\License\License_Manager;
use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class License_Handler_Setup
{
    /**
     * @var License_Manager
     */
    private $license_manager;

    /**
     * @since 0.8.12
     * @param License_Manager $license_manager
     */
    public function __construct(License_Manager $license_manager)
    {
        $this->license_manager = $license_manager;
    }

    /**
     * Make all license handlers available in Affilicious.
     *
     * @hook
     * @since 0.8.12
     */
    public function init()
    {
        do_action('aff_license_handler_before_init');

        $license_handlers = apply_filters('aff_license_handler_init', array());
        Assert_Helper::is_array($license_handlers, __METHOD__, 'Expected the license handlers to be an array. Got: %s', '0.9.2');

        foreach ($license_handlers as $license_handler) {
            $this->license_manager->add_license_handler($license_handler);
        }

        do_action('aff_license_handler_after_init', $license_handlers);
    }
}
