<?php
namespace Affilicious\Shop\Factory\In_Memory;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.6
 */
class In_Memory_Shop_Template_Factory implements Shop_Template_Factory_Interface
{
    /**
     * @var Slug_Generator_Interface
     */
    protected $slug_generator;

    /**
     * @since 0.6
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(Slug_Generator_Interface $slug_generator)
    {
        $this->slug_generator = $slug_generator;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Name $name, Slug $slug)
    {
        do_action('aff_shop_template_factory_before_create', $name, $slug);

        $shop_template = new Shop_Template($name, $slug);
        $shop_template = apply_filters('aff_shop_template_factory_create', $shop_template);

        do_action('aff_shop_template_factory_after_create', $shop_template, $name, $slug);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create_from_name(Name $name)
    {
        $slug = $this->slug_generator->generate_from_name($name);
        $shop_template = $this->create($name, $slug);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function create_from_shop(Shop $shop)
    {
        $shop_template = $this->create($shop->get_name(), $shop->get_slug());

        return $shop_template;
    }
}
