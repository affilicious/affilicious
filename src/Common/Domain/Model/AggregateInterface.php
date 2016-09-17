<?php
namespace Affilicious\Common\Domain\Model;

interface AggregateInterface
{
    /**
     * @since 0.5.2
     * @param mixed $object
     * @return bool
     */
    public function isEqualTo($object);
}
