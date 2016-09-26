<?php
namespace Affilicious\Product\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InvalidOptionException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param mixed $invalidOption
     * @param array[] $validOptions
     */
    public function __construct($invalidOption, $validOptions)
    {
        parent::__construct(sprintf(
            __('Invalid option: %s. Please choose one of the following options: %s.', 'affilicious'),
            $invalidOption,
            implode(', ', $validOptions)
        ));
    }
}
