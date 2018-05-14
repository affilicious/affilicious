<?php
namespace Affilicious\Common\License;

use Affilicious\Common\Admin\License\License_Handler_Interface as Base_License_Handler_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.0 'Use Affilicious\Common\Admin\License\License_Handler_Interface' instead
 * @since 0.9
 */
interface License_Handler_Interface extends Base_License_Handler_Interface
{
}
