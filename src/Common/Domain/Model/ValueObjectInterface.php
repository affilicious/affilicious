<?php
namespace Affilicious\Common\Domain\Model;

interface ValueObjectInterface
{
    /**
     * @since 0.5.2
     * @param mixed $value
     */
    public function __construct($value);

    /**
     * @since 0.5.2
     * @return mixed
     */
    public function getValue();

    /**
     * @since 0.5.2
     * @param mixed $object
     * @return bool
     */
    public function isEqualTo($object);

    /**
     * @since 0.5.2
     * @return string
     */
    public function __toString();
}
