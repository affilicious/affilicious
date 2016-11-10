<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Value_Object_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Credentials_Interface extends Value_Object_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct($value);

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_value();
}
