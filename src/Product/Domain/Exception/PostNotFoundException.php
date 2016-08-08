<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Exception;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class PostNotFoundException extends \RuntimeException
{
    /**
     * @param string|int $postId
     */
    public function __construct($postId)
    {
        parent::__construct(sprintf(
            __("The post #%s wasn't found.", 'affilicious-products'),
            $postId
        ));
    }
}
