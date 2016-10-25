<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailTemplateFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new detail template which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @return DetailTemplate
     */
    public function create(Title $title, Type $type);
}
