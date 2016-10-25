<?php
namespace Affilicious\Detail\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\DetailGroup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DuplicatedDetailException extends DomainException
{
    /**
     * @since 0.6
     * @param Detail $detail
     * @param DetailGroup $detailGroup
     */
    public function __construct(Detail $detail, DetailGroup $detailGroup)
    {
        parent::__construct(sprintf(
            'The detail %s (%s) does already exist in the detail group #%s (%s)',
            $detail->getName()->getValue(),
            $detail->getTitle()->getValue(),
            $detailGroup->getName()->getValue(),
            $detailGroup->getTitle()->getValue()
        ));
    }
}
