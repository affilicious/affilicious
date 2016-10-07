<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId;
use Affilicious\Common\Domain\Exception\DomainException;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeTemplateGroupDatabaseException extends DomainException
{
    /**
     * @since 0.6
     * @param AttributeTemplateGroupId|string|int $attributeTemplateGroupId
     */
    public function __construct($attributeTemplateGroupId)
    {
        parent::__construct(sprintf(
            'An error related to the attribute template group #%s has occurred in the database.',
            $attributeTemplateGroupId
        ));
    }
}
