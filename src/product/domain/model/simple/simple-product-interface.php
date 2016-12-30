<?php
namespace Affilicious\Product\Domain\Model\Simple;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Model\Detail_Group_Aware_Product_Interface as Detail_Group_Aware;
use Affilicious\Product\Domain\Model\Image_Gallery_Aware_Product_Interface as Image_Gallery_Aware;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Relation_Aware_Product_Interface as Relation_Aware;
use Affilicious\Product\Domain\Model\Review_Aware_Product_Interface as Review_Aware;
use Affilicious\Product\Domain\Model\Shop_Aware_Product_Interface as Shop_Aware;
use Affilicious\Product\Domain\Model\Tag_Aware_Product_Interface as Tag_Aware;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Simple_Product_Interface extends Product_Interface, Shop_Aware, Relation_Aware, Review_Aware, Image_Gallery_Aware, Detail_Group_Aware, Tag_Aware
{
    /**
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key);
}
