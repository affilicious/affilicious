<?php
namespace Affilicious\Common\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class PostNotFoundException extends \RuntimeException
{
    /**
     * @since 0.3
     * @param string|int $postVariantGroupId
     */
    public function __construct($postVariantGroupId)
    {
        parent::__construct(sprintf(
            "The post #%s wasn't found.",
            $postVariantGroupId
        ));
    }
}
