<?php
namespace Affilicious\Attribute\Domain\Model\AttributeTemplate;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeTemplateFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new attribute template which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @return AttributeTemplate
     */
    public function create(Title $title, Type $type);
}
