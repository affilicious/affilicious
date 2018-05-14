<?php
namespace Affilicious\Detail\Factory\In_Memory;

use Affilicious\Detail\Factory\Detail_Template_Factory_Interface;
use Affilicious\Detail\Model\Detail;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Generator\Slug_Generator_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class In_Memory_Detail_Template_Factory implements Detail_Template_Factory_Interface
{
    /**
     * The slug generator is responsible to auto-generating slugs.
     *
     * @since 0.8
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
        do_action('aff_detail_template_factory_before_create');

        $detail_template = new Detail_Template($name, $slug, $type, $unit);
        $detail_template = apply_filters('aff_detail_template_factory_create', $detail_template);

        do_action('aff_detail_template_factory_after_create', $detail_template);

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

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function create_from_detail(Detail $detail)
    {
        $detail_template = $this->create(
            $detail->get_name(),
            $detail->get_slug(),
            $detail->get_type(),
            $detail->get_unit()
        );

        return $detail_template;
    }
}
