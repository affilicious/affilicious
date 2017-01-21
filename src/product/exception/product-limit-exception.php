<?php
namespace Affilicious\Product\Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product_Limit_Exception extends \RuntimeException
{
    /**
     * @since 0.7
     * @param int $limit
     */
    public function __construct($limit)
    {
        parent::__construct(sprintf(
            'Reached the max product limit of %d.',
            $limit
        ));
    }
}
