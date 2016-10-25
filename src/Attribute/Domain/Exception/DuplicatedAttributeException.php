<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Common\Domain\Exception\DomainException;

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
