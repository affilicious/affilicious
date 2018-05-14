<?php
namespace Affilicious\Detail\Factory;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Detail\Model\Detail;
use Affilicious\Detail\Model\Detail_Template;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
interface Detail_Template_Factory_Interface
{
    /**
     * Create a new detail template.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Type $type
     * @param null|Unit $unit
     * @return Detail_Template
     */
    public function create(Name $name, Slug $slug, Type $type, Unit $unit = null);

    /**
     * Create a new detail template.
     * The slug is auto-generated from the name.
     *
     * @since 0.8
     * @param Name $name
     * @param Type $type
     * @param null|Unit $unit
     * @return Detail_Template
     */
    public function create_from_name(Name $name, Type $type, Unit $unit = null);

    /**
     * Create a new detail template by the detail.
     *
     * @since 0.9
     * @param Detail $detail
     * @return Detail_Template
     */
    public function create_from_detail(Detail $detail);
}
