<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new simple product which can be stored into a database.
     *
     * @since 0.6
     * @param Title $title
     * @return Product
     */
    public function create(Title $title);
}
