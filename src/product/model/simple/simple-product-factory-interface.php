<?php
namespace Affilicious\Product\Model\Simple;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Simple_Product_Factory_Interface
{
    /**
     * Create a new simple product which can be stored into the database.
     *
     * @since 0.7
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     * @return Simple_Product_Interface
     */
    public function create(Name $title, Slug $name, Key $key);
}
