<?php
namespace Affilicious\Shop\Domain\Exception;

use Affilicious\Common\Domain\Exception\DomainException;
use Affilicious\Shop\Domain\Model\ShopId;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class MissingShopException extends DomainException
{
    /**
     * @since 0.6
     * @param ShopId $shopVariantId
     */
    public function __construct(ShopId $shopVariantId)
    {
        parent::__construct(sprintf(
            'The shop #%s is missing in the database.',
            $shopVariantId
        ));
    }
}
