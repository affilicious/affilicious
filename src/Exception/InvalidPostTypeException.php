<?php
namespace Affilicious\ProductsPlugin\Exception;

class InvalidPostTypeException extends \RuntimeException
{
    /**
     * @param string $invalidPostType
     * @param string $validPostType
     */
    public function __construct($invalidPostType, $validPostType)
    {
        parent::__construct(sprintf(
            __('Invalid post type: %s. It should be: %s', 'affiliciousproducts'),
            $invalidPostType,
            $validPostType
        ));
    }
}
