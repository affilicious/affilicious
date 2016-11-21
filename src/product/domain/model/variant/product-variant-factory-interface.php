<?php
namespace Affilicious\Product\Domain\Model\Variant;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Variant_Factory_Interface extends Factory_Interface
{
    /**
     * Create a new product variant which can be stored into the database.
     *
     * @since 0.7
     * @param Complex_Product_Interface $parent
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @return Product_Variant_Interface
     */
    public function create(Complex_Product_Interface $parent, Title $title, Name $name, Key $key);
}
