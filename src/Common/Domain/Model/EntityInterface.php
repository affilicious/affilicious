<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface EntityInterface
{
    /**
     * @since 0.6
     * @return ValueObjectInterface
     */
    public function getId();

    /**
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function isEqualTo($object);
}
