<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Exception;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class InvalidPostTypeException extends \RuntimeException
{
    /**
     * @param string $invalidPostType
     * @param string $validPostType
     */
    public function __construct($invalidPostType, $validPostType)
    {
        parent::__construct(sprintf(
            __('Invalid post type: %s. It should be: %s', 'affilicious-products'),
            $invalidPostType,
            $validPostType
        ));
    }
}
