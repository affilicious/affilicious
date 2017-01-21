<?php
namespace Affilicious\Product\Model\Variant;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Complex\Complex_Product_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Variant_Factory_Interface
{
    /**
     * Create a new product variant which can be stored into the database.
     *
     * @since 0.7
     * @param Complex_Product_Interface $parent
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     * @return Product_Variant_Interface
     */
    public function create(Complex_Product_Interface $parent, Name $title, Slug $name, Key $key);
}
