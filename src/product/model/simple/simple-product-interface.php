<?php
namespace Affilicious\Product\Model\Simple;

use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Product\Model\Detail_Group_Aware_Product_Interface as Detail_Group_Aware;
use Affilicious\Product\Model\Image_Gallery_Aware_Product_Interface as Image_Gallery_Aware;
use Affilicious\Product\Model\Product_Interface;
use Affilicious\Product\Model\Relation_Aware_Product_Interface as Relation_Aware;
use Affilicious\Product\Model\Review_Aware_Product_Interface as Review_Aware;
use Affilicious\Product\Model\Shop_Aware_Product_Interface as Shop_Aware;
use Affilicious\Product\Model\Tag_Aware_Product_Interface as Tag_Aware;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Simple_Product_Interface extends Product_Interface, Shop_Aware, Relation_Aware, Review_Aware, Image_Gallery_Aware, Detail_Group_Aware, Tag_Aware
{
    /**
     * @since 0.7
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     */
    public function __construct(Name $title, Slug $name, Key $key);
}
