<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new product which can be stored into a database.
     *
     * @since 0.6
     * @param Title $title
     * @return Product
     */
    public function create(Title $title);
}
