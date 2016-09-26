<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AggregateInterface
{
    /**
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function isEqualTo($object);
}
