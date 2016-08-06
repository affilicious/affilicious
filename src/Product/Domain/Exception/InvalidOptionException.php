<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Exception;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class InvalidOptionException extends \RuntimeException
{
    /**
     * @param mixed $invalidOption
     * @param array[] $validOptions
     */
    public function __construct($invalidOption, $validOptions)
    {
        parent::__construct(sprintf(
            __('Invalid option: %s. Please choose one of the following options: %s.', 'affiliciousproducts'),
            $invalidOption,
            implode(', ', $validOptions)
        ));
    }
}
