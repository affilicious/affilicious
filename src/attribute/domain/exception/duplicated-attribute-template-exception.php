<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Common\Domain\Exception\Domain_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Attribute_Template_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Attribute_Template $shop_template
     * @param Attribute_Template_Group $attribute_template_group
     */
    public function __construct(Attribute_Template $shop_template, Attribute_Template_Group $attribute_template_group)
    {
        parent::__construct(sprintf(
            'The attribute template %s (%s) does already exist in the attribute template group #%s (%s)',
            $shop_template->get_name()->get_value(),
            $shop_template->get_title()->get_value(),
            $attribute_template_group->get_name()->get_value(),
            $attribute_template_group->get_title()->get_value()
        ));
    }
}
