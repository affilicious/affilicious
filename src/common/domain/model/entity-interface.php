<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Entity_interface
{
    /**
     * @since 0.6
     * @return Value_Object_Interface
     */
    public function get_id();

    /**
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function is_equal_to($object);
}
