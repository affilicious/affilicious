<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface AttributeFactoryInterface extends FactoryInterface
{
    /**
     * Create a new attribute
     *
     * @since 0.6
     * @param Title $title
     * @param Type $type
     * @param Value $value
     * @return Attribute
     */
    public function create(Title $title, Type $type, Value $value);
}
