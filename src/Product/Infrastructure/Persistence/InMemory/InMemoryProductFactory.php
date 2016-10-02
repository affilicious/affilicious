<?php
namespace Affilicious\Product\Infrastructure\Persistence\InMemory;

use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Product;
use Affilicious\Product\Domain\Model\ProductFactoryInterface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryProductFactory implements ProductFactoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title)
    {
        $product = new Product(
            $title,
            $title->toName()
        );

        return $product;
    }
}
