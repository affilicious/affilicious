<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeGroupFactoryInterface extends FactoryInterface
{
    /**
     * Create a new attribute group
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return AttributeGroup
     */
    public function create(Title $title, Name $name);
}
