<?php
namespace Affilicious\Common\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Setup
{
    /**
     * Init the carbon fields
     *
     * @since 0.3
     */
    public function crb_init_carbon_field_hidden() {
        if (class_exists("Carbon_Fields\\Field")) {
            require_once(dirname(__FILE__) . '/../form/carbon/hidden-field.php');
            require_once(dirname(__FILE__) . '/../form/carbon/number-field.php');
            require_once(dirname(__FILE__) . '/../form/carbon/password-field.php');
            require_once(dirname(__FILE__) . '/../form/carbon/tags-field.php');
        }
    }
}
