<?php
namespace Affilicious\Detail\Domain\Exception;

use Affilicious\Common\Domain\Exception\Domain_Exception;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Detail_Template_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Detail_Template $detail_template
     * @param Detail_Template_Group $detail_template_group
     */
    public function __construct(Detail_Template $detail_template, Detail_Template_Group $detail_template_group)
    {
        parent::__construct(sprintf(
            'The detail template %s (%s) does already exist in the detail template group #%s (%s)',
            $detail_template->get_name()->get_value(),
            $detail_template->get_title()->get_value(),
            $detail_template_group->get_name()->get_value(),
            $detail_template_group->get_title()->get_value()
        ));
    }
}
