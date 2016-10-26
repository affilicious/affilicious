<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\Domain_Exception;
use Affilicious\Shop\Domain\Model\Shop_Template_Id;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop_Template_Database_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Shop_Template_Id|string|int $shop_template_group_id
     */
    public function __construct($shop_template_group_id)
    {
        parent::__construct(sprintf(
            'An error related to the shop template #%s has occurred in the database.',
            $shop_template_group_id
        ));
    }
}
