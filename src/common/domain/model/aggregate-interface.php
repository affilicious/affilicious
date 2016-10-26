<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Aggregate_Interface
{
    /**
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function is_equal_to($object);
}
