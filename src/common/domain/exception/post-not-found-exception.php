<?php
namespace Affilicious\Common\Domain\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Post_Not_Found_Exception extends \RuntimeException
{
    /**
     * @since 0.6
     * @param string|int $post_variant_group_id
     */
    public function __construct($post_variant_group_id)
    {
        parent::__construct(sprintf(
            "The post #%s wasn't found.",
            $post_variant_group_id
        ));
    }
}
