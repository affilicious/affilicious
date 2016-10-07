<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeTemplateGroupFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new attribute template group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return AttributeTemplateGroup
     */
    public function create(Title $title, Name $name);
}
