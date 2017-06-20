<?php
namespace Affilicious\Shop\Factory;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Template_Factory_Interface
{
    /**
     * Create a new shop template.
     *
     * @since 0.8
     * @param Name|Slug $name
     * @param Slug $slug
     * @return Shop_Template
     */
    public function create(Name $name, Slug $slug);

    /**
     * Create a new shop template.
     * The slug and key are auto-generated from the name.
     *
     * @since 0.8
     * @param Name $name
     * @return Shop_Template
     */
    public function create_from_name(Name $name);

    /**
     * Create a new shop template by the shop.
     *
     * @since 0.9
     * @param Shop $shop
     * @return Shop_Template
     */
    public function create_from_shop(Shop $shop);
}
