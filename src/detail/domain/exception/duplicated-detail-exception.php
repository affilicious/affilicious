<?php
namespace Affilicious\Detail\Domain\Exception;

use Affilicious\Common\Domain\Exception\Domain_Exception;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail_Group;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Detail_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Detail $detail
     * @param Detail_Group $detail_group
     */
    public function __construct(Detail $detail, Detail_Group $detail_group)
    {
        parent::__construct(sprintf(
            'The detail %s (%s) does already exist in the detail group #%s (%s)',
            $detail->get_name()->get_value(),
            $detail->get_title()->get_value(),
            $detail_group->get_name()->get_value(),
            $detail_group->get_title()->get_value()
        ));
    }
}
