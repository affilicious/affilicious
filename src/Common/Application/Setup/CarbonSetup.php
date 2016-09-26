<?php
namespace Affilicious\Common\Application\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonSetup
{
    /**
     * Init the hidden Carbon field
     *
     * @since 0.3
     */
    public function crb_init_carbon_field_hidden() {
        if (class_exists("Carbon_Fields\\Field")) {
            require_once(dirname(__FILE__) . '/../Form/Carbon/Hidden_Field.php');
            require_once(dirname(__FILE__) . '/../Form/Carbon/Number_Field.php');
        }
    }
}
