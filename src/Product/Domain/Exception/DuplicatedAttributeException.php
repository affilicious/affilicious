<?php
namespace Affilicious\Product\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Product\Domain\Model\AttributeGroup\Attribute\Attribute;
use Affilicious\Product\Domain\Model\AttributeGroup\AttributeGroup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedAttributeException extends DomainException
{
    /**
     * @since 0.6
     * @param Attribute $attribute
     * @param AttributeGroup $attributeGroup
     */
    public function __construct(Attribute $attribute, AttributeGroup $attributeGroup)
    {
        parent::__construct(sprintf(
            'The attribute %s (%s) does already exist in the attribute group #%s (%s)',
            $attribute->getName()->getValue(),
            $attribute->getTitle()->getValue(),
            $attributeGroup->getName()->getValue(),
            $attributeGroup->getTitle()->getValue()
        ));
    }
}
