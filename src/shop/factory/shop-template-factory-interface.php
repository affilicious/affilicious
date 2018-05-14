<?php
namespace Affilicious\Shop\Factory;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
interface Shop_Template_Factory_Interface
{
    /**
     * Create a new shop template.
     *
     * @since 0.8
     * @param Name $name The new shop template name.
     * @param Slug $slug The new shop template slug.
     * @return Shop_Template The created shop template.
     */
    public function create(Name $name, Slug $slug);

    /**
     * Create a new shop template.
     * The slug will be auto-generated from the name.
     *
     * @since 0.8
     * @param Name $name The new shop template name.
     * @return Shop_Template The created shop template.
     */
    public function create_from_name(Name $name);

    /**
     * Create a new shop template by the shop.
     *
     * @since 0.9
     * @param Shop $shop The shop to create the shop template from.
     * @return Shop_Template The created shop template.
     */
    public function create_from_shop(Shop $shop);
}
