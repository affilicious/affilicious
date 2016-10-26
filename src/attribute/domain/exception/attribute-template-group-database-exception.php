<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id;
use Affilicious\Common\Domain\Exception\Domain_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Group_Database_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Attribute_Template_Group_Id|string|int $shop_template_group_id
     */
    public function __construct($shop_template_group_id)
    {
        parent::__construct(sprintf(
            'An error related to the attribute template group #%s has occurred in the database.',
            $shop_template_group_id
        ));
    }
}
