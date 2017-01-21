<?php
namespace Affilicious\Detail\Factory\In_Memory;

use Affilicious\Detail\Factory\Detail_Template_Factory_Interface;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Generator_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Detail_Template_Factory implements Detail_Template_Factory_Interface
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
        do_action('affilicious_detail_template_factory_before_create');

        $detail_template = new Detail_Template($name, $slug, $type, $unit);

        do_action('affilicious_detail_template_factory_after_create', $detail_template);

        return $detail_template;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function create_from_name(Name $name, Type $type, Unit $unit = null)
    {
        $detail_template = $this->create(
            $name,
            $this->slug_generator->generate_from_name($name),
            $type,
            $unit
        );

        return $detail_template;
    }
}
