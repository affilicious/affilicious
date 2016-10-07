<?php
namespace Affilicious\Product\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailFactoryInterface extends FactoryInterface
{

    public function create(
        DetailTemplateGroupId $templateGroupId,
        DetailTemplateKey $templateKey,
        Title $title,
        Type $type
    );
}
