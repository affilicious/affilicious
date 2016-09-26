<?php
namespace Affilicious\Product\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class PostNotFoundException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string|int $postId
     */
    public function __construct($postId)
    {
        parent::__construct(sprintf(
            __("The post #%s wasn't found.", 'affilicious'),
            $postId
        ));
    }
}
