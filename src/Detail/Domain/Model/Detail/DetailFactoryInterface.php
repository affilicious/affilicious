<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailFactoryInterface extends FactoryInterface
{
    /**
     * Create a new detail
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @return Detail
     */
    public function create(Title $title, Type $type);
}
