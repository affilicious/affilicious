<?php
namespace Affilicious\Attribute\Factory;

use Affilicious\Attribute\Model\Attribute_Template;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Unit;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Attribute_Template_Factory_Interface
{
    /**
     * Create a new attribute template.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Type $type
     * @param null|Unit $unit
     * @return Attribute_Template
     */
    public function create(Name $name, Slug $slug, Type $type, Unit $unit = null);

    /**
     * Create a new attribute template.
     * The slug is auto-generated from the name.
     *
     * @since 0.8
     * @param Name $name
     * @param Type $type
     * @param null|Unit $unit
     * @return Attribute_Template
     */
    public function create_from_name(Name $name, Type $type, Unit $unit = null);
}
