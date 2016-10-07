<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Shop\Domain\Model\ShopTemplateId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ShopTemplateDatabaseException extends DomainException
{
    /**
     * @since 0.6
     * @param ShopTemplateId|string|int $attributeTemplateGroupId
     */
    public function __construct($attributeTemplateGroupId)
    {
        parent::__construct(sprintf(
            'An error related to the shop template #%s has occurred in the database.',
            $attributeTemplateGroupId
        ));
    }
}
