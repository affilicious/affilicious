<?php
namespace Affilicious\Common\Exception;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Domain_Exception extends \RuntimeException
{
}
