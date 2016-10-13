<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\Product;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class MissingProductIdException extends DomainException
{
    /**
     * @since 0.6
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        parent::__construct(sprintf(
            'The product id for "%s" is missing.',
            $product
        ));
    }
}
