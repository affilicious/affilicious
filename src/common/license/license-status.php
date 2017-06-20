<?php
namespace Affilicious\Common\License;

use Affilicious\Common\Admin\License\License_Status as Base_License_Status;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.1 Use Affilicious\Common\Admin\License\License_Status
 */
class License_Status extends Base_License_Status
{
}
