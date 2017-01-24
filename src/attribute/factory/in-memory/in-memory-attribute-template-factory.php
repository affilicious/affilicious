<?php
namespace Affilicious\Attribute\Factory\In_Memory;

use Affilicious\Attribute\Factory\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Unit;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Generator\Slug_Generator_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Attribute_Template_Factory implements Attribute_Template_Factory_Interface
{
    /**
     * The slug generator is responsible to auto-generating slugs.
     *
     * @var Slug_Generator_Interface
     */
    protected $slug_generator;

    /**
     * @since 0.8
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(Slug_Generator_Interface $slug_generator)
    {
        $this->slug_generator = $slug_generator;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create(Name $name, Slug $slug, Type $type, Unit $unit = null)
    {
        do_action('affilicious_attribute_template_factory_before_create');

        $attribute_template = new Attribute_Template($name, $slug, $type, $unit);

        do_action('affilicious_attribute_template_factory_after_create', $attribute_template);

        return $attribute_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create_from_name(Name $name, Type $type, Unit $unit = null)
    {
        $attribute_template = $this->create(
            $name,
            $this->slug_generator->generate_from_name($name),
            $type,
            $unit
        );

        return $attribute_template;
    }
}
