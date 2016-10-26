<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\Post_Not_Found_Exception;
use Affilicious\Shop\Domain\Model\Shop_Template_Id;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop_Template_Not_Found_Exception extends Post_Not_Found_Exception
{
    /**
     * @since 0.6
     * @param Shop_Template_Id|string|int $shop_template_group_id
     */
    public function __construct($shop_template_group_id)
    {
        parent::__construct(sprintf(
            "The shop template #%s wasn't found.",
            $shop_template_group_id
        ));
    }
}
