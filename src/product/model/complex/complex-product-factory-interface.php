<?php
namespace Affilicious\Product\Model\Complex;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Complex_Product_Factory_Interface
{
    /**
     * Create a new complex product which can be stored into the database.
     *
     * @since 0.7
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     * @return Complex_Product_Interface
     */
    public function create(Name $title, Slug $name, Key $key);
}
