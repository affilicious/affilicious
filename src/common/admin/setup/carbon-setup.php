<?php
namespace Affilicious\Common\Admin\Setup;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Carbon_Setup
{
    /**
     * Init the carbon fields
     *
     * @since 0.9
     */
    public function init()
    {
        if (class_exists("Carbon_Fields\\Field")) {
            require_once(__DIR__ . '/../form/carbon/hidden-field.php');
            require_once(__DIR__ . '/../form/carbon/number-field.php');
            require_once(__DIR__ . '/../form/carbon/password-field.php');
            require_once(__DIR__ . '/../form/carbon/tags-field.php');
            require_once(__DIR__ . '/../form/carbon/image-gallery-field.php');
        }
    }
}
