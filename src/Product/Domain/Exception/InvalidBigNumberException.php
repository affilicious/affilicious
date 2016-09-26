<?php
namespace Affilicious\Product\Domain\Exception;

class InvalidBigNumberException extends \RuntimeException
{
    /**
     * @since 0.5.2
     * @param int $value
     * @param int $min
     */
    public function __construct($value, $min)
    {
        parent::__construct(sprintf(
            'The given number %s is too small. It has to be greater than or equal to %s',
            $value,
            $min
        ));
    }
}
