<?php
namespace Affilicious\ProductsPlugin\Common\Application\Setup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonSetup
{
    /**
     * Init the hidden Carbon field
     */
    public function crb_init_carbon_field_hidden() {
        if (class_exists("Carbon_Fields\\Field")) {
            require_once(dirname(__FILE__) . '/../Form/Carbon/Hidden_Field.php');
        }
    }
}
