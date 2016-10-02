<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailGroupFactoryInterface extends FactoryInterface
{
    /**
     * Create a new detail group
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @return DetailGroup
     */
    public function create(Title $title, Name $name);
}
