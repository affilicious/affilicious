<?php
namespace Affilicious\Detail\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplate;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedDetailTemplateException extends DomainException
{
    /**
     * @since 0.6
     * @param DetailTemplate $detailTemplate
     * @param DetailTemplateGroup $detailTemplateGroup
     */
    public function __construct(DetailTemplate $detailTemplate, DetailTemplateGroup $detailTemplateGroup)
    {
        parent::__construct(sprintf(
            'The detail template %s (%s) does already exist in the detail group #%s (%s)',
            $detailTemplate->getName()->getValue(),
            $detailTemplate->getTitle()->getValue(),
            $detailTemplateGroup->getName()->getValue(),
            $detailTemplateGroup->getTitle()->getValue()
        ));
    }
}
