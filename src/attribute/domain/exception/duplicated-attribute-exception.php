<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Common\Domain\Exception\Domain_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Attribute_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Attribute $shop
     * @param Attribute_Group $attribute_group
     */
    public function __construct(Attribute $shop, Attribute_Group $attribute_group)
    {
        parent::__construct(sprintf(
            'The attribute %s (%s) does already exist in the attribute group #%s (%s)',
            $shop->get_name()->get_value(),
            $shop->get_title()->get_value(),
            $attribute_group->get_name()->get_value(),
            $attribute_group->get_title()->get_value()
        ));
    }
}
