<?php
namespace Affilicious\Shop\Factory\In_Memory;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Model\Shop_Template;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Shop_Template_Factory implements Shop_Template_Factory_Interface
{
    /**
     * @var Slug_Generator_Interface
     */
    protected $slug_generator;

    /**
     * @var Key_Generator_Interface
     */
    protected $key_generator;

    /**
     * @since 1.0
     * @param Slug_Generator_Interface $slug_generator
     * @param Key_Generator_Interface $key_generator
     */
    public function __construct(Slug_Generator_Interface $slug_generator, Key_Generator_Interface $key_generator) {
        $this->slug_generator = $slug_generator;
        $this->key_generator = $key_generator;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Name $name, Slug $slug, Key $key)
    {
        $shop_template = new Shop_Template($name, $slug, $key);

        return $shop_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create_from_name(Name $name)
    {
        $slug = $this->slug_generator->generate_from_name($name);
        $key = $this->key_generator->generate_from_name($name);
        $shop_template = $this->create($name, $slug, $key);

        return $shop_template;
    }
}
