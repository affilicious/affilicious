<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\PostNotFoundException;
use Affilicious\Shop\Domain\Model\ShopTemplateId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ShopTemplateNotFoundException extends PostNotFoundException
{
    /**
     * @since 0.6
     * @param ShopTemplateId|string|int $attributeTemplateGroupId
     */
    public function __construct($attributeTemplateGroupId)
    {
        parent::__construct(sprintf(
            "The shop template #%s wasn't found.",
            $attributeTemplateGroupId
        ));
    }
}
