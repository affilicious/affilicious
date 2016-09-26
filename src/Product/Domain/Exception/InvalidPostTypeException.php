<?php
namespace Affilicious\Product\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InvalidPostTypeException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string $invalidPostType
     * @param string $validPostType
     */
    public function __construct($invalidPostType, $validPostType)
    {
        parent::__construct(sprintf(
            __('Invalid post type: %s. It should be: %s', 'affilicious'),
            $invalidPostType,
            $validPostType
        ));
    }
}
