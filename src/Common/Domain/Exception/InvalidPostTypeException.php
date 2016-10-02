<?php
namespace Affilicious\Common\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InvalidPostTypeException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string $invalidPostType
     * @param string|array $validPostType
     */
    public function __construct($invalidPostType, $validPostType)
    {
        parent::__construct(sprintf(
            'Invalid post type: %s. Pleas use: %s',
            $invalidPostType,
            is_array($validPostType) ? implode(',', $validPostType) : $validPostType
        ));
    }
}
