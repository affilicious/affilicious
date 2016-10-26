<?php
namespace Affilicious\Common\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Invalid_Post_Type_Exception extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string $invalid_post_type
     * @param string|array $valid_post_type
     */
    public function __construct($invalid_post_type, $valid_post_type)
    {
        parent::__construct(sprintf(
            'Invalid post type: %s. _pleas use: %s',
            $invalid_post_type,
            is_array($valid_post_type) ? implode(',', $valid_post_type) : $valid_post_type
        ));
    }
}
