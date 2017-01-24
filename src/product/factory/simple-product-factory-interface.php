<?php
namespace Affilicious\Product\Factory;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Simple_Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Simple_Product_Factory_Interface
{
    /**
     * Create a new simple product.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @return Simple_Product
     */
    public function create(Name $name, Slug $slug);
}
