<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Domain\Model\FactoryInterface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface DetailGroupFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new detail group which can be stored into the database.
     *
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @return DetailGroup
     */
    public function create(Title $title, Name $name, Key $key);

    /**
     * Create a new detail group from the template.
     *
     * @since 0.6
     * @param DetailTemplateGroupId $detailTemplateGroupId
     * @param mixed $data The structure of the data varies and depends on the implementation
     * @return DetailGroup
     */
    public function createFromTemplateIdAndData(DetailTemplateGroupId $detailTemplateGroupId, $data);
}
