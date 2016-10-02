<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Product;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductVariantFactoryInterface extends FactoryInterface
{
    /**
     * Create a new product variant which can be stored into a database
     *
     * @since 0.6
     * @param Product $parentProduct
     * @param Title $title
     * @return ProductVariant
     */
    public function create(Product $parentProduct, Title $title);
}
