<?php
namespace Affilicious\Product\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InvalidSmallNumberException extends \RuntimeException
{
    /**
     * @since 0.6
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
