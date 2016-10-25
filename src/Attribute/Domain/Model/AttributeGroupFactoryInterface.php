<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeGroupFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new attribute group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @return AttributeGroup
     */
    public function create(Title $title, Name $name, Key $key);

    /**
     * Create a new attribute group from the template.
     *
     * @since 0.6
     * @param AttributeTemplateGroupId $attributeTemplateGroupId
     * @param mixed $data The structure of the data varies and depends on the implementation
     * @return AttributeGroup
     */
    public function createFromTemplateIdAndData(AttributeTemplateGroupId $attributeTemplateGroupId, $data);
}
