<?php
namespace Affilicious\Attribute\Domain\Exception;

use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id;
use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Template_Group_Not_Found_Exception extends Post_Not_Found_Exception
{
    /**
     * @since 0.6
     * @param Attribute_Template_Group_Id|string|int $shop_template_group_id
     */
    public function __construct($shop_template_group_id)
    {
        parent::__construct(sprintf(
            "The attribute template group #%s wasn't found.",
            $shop_template_group_id
        ));
    }
}
