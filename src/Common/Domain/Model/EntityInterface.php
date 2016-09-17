<?php
namespace Affilicious\Common\Domain\Model;

interface EntityInterface
{
    /**
     * @since 0.5.2
     * @return ValueObjectInterface
     */
    public function getId();

    /**
     * @since 0.5.2
     * @param mixed $object
     * @return bool
     */
    public function isEqualTo($object);
}
