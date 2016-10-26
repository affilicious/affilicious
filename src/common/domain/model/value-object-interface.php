<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Value_Object_Interface
{
    /**
     * @since 0.6
     * @param mixed $value
     */
    public function __construct($value);

    /**
     * @since 0.6
     * @return mixed
     */
    public function get_value();

    /**
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function is_equal_to($object);

    /**
     * @since 0.6
     * @return string
     */
    public function __toString();
}
