<?php
namespace Affilicious\Common\License;

use Affilicious\Common\Admin\License\License_Manager as Base_License_Manager;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.0 'Use Affilicious\Common\Admin\License\License_Manager' instead
 * @since 0.9
 */
final class License_Manager extends Base_License_Manager
{
}
