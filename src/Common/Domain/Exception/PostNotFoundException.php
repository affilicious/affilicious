<?php
namespace Affilicious\Common\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class PostNotFoundException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string|int $postVariantId
     */
    public function __construct($postVariantId)
    {
        parent::__construct(sprintf(
            "The post #%s wasn't found.",
            $postVariantId
        ));
    }
}
