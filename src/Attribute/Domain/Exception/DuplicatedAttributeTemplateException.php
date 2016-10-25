<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\AttributeTemplateGroup;
use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Attribute\Domain\Model\AttributeTemplate;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedAttributeTemplateException extends DomainException
{
    /**
     * @since 0.6
     * @param AttributeTemplate $attributeTemplate
     * @param AttributeTemplateGroup $attributeTemplateGroup
     */
    public function __construct(AttributeTemplate $attributeTemplate, AttributeTemplateGroup $attributeTemplateGroup)
    {
        parent::__construct(sprintf(
            'The attribute template %s (%s) does already exist in the attribute group #%s (%s)',
            $attributeTemplate->getName()->getValue(),
            $attributeTemplate->getTitle()->getValue(),
            $attributeTemplateGroup->getName()->getValue(),
            $attributeTemplateGroup->getTitle()->getValue()
        ));
    }
}
