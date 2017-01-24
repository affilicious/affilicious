<?php
namespace Affilicious\Product\Exception;

use Affilicious\Common\Exception\Domain_Exception;
use Affilicious\Detail\Model\Detail_Group;
use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Duplicated_Detail_Group_Exception extends Domain_Exception
{
    /**
     * @since 0.6
     * @param Detail_Group $detail_group
     * @param Product $product
     */
    public function __construct(Detail_Group $detail_group, Product $product)
    {
        parent::__construct(sprintf(
            'The detail group %s (%s) does already exist in the product #%s (%s)',
            $detail_group->get_name()->get_value(),
            $detail_group->get_title()->get_value(),
            $product->get_id()->get_value(),
            $product->get_title()->get_value()
        ));
    }
}
