<?php
namespace Affilicious\Product\Domain\Model\Simple;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Product\Domain\Model\Abstract_Product;
use Affilicious\Product\Domain\Model\Product_Id;
use Affilicious\Product\Domain\Model\Review\Review_Interface;
use Affilicious\Product\Domain\Model\Tag;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Simple_Product extends Abstract_Product implements Simple_Product_Interface
{
    /**
     * Stores the image gallery.
     *
     * @var Image[]
     */
    protected $image_gallery;

    /**
     * Stores the product tags like "test winner" or "best price".
     *
     * @var Tag[]
     */
    protected $tags;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        parent::__construct($title, $name, $key, Type::simple());
        $this->detail_groups = array();
        $this->related_products = array();
        $this->related_accessories = array();
        $this->image_gallery = array();
        $this->tags = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_shop(Affiliate_Link $affiliate_link)
    {
        return isset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_shop(Shop_Interface $shop)
    {
        $this->shops[$shop->get_affiliate_link()->get_value()] = $shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_shop(Affiliate_Link $affiliate_link)
    {
        unset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shop(Affiliate_Link $affiliate_link)
    {
        if(!$this->has_shop($affiliate_link)) {
            return null;
        }

        $shop = $this->shops[$affiliate_link->get_value()];

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_cheapest_shop()
    {
        /** @var Shop_Interface $cheapest_shop */
        $cheapest_shop = null;
        foreach ($this->shops as $shop) {
            if ($cheapest_shop === null ||
                ($cheapest_shop->has_price() && $cheapest_shop->get_price()->is_greater_than($shop->has_price()))) {
                $cheapest_shop = $shop;
            }
        }

        return $cheapest_shop;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_shops()
    {
        $shops = array_values($this->shops);

        return $shops;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_shops($shops)
    {
        $this->shops = array();

        foreach ($shops as $shop) {
            $this->add_shop($shop);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_detail_group(Name $name)
    {
        return isset($this->detail_groups[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function add_detail_group(Detail_Group $detail_group)
    {
        /*if($this->has_detail_group($detail_group->get_name())) {
            throw new Duplicated_Detail_Group_Exception($detail_group, $this);
        }*/

        $this->detail_groups[$detail_group->get_name()->get_value()] = $detail_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function remove_detail_group(Name $name)
    {
        unset($this->detail_groups[$name->get_value()]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_group(Name $name)
    {
        if(!$this->has_detail_group($name)) {
            return null;
        }

        $detail_group = $this->detail_groups[$name->get_value()];

        return $detail_group;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_detail_groups()
    {
        $detail_groups = array_values($this->detail_groups);

        return $detail_groups;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_detail_groups($detail_groups)
    {
        $this->detail_groups = array();

        // add_detail_group checks for the type
        foreach ($detail_groups as $detail) {
            $this->add_detail_group($detail);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_review()
    {
        return $this->review !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_review()
    {
        return $this->review;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_review($review)
    {
        if($review !== null && !($review instanceof Review_Interface)) {
            throw new Invalid_Type_Exception($review, Review_Interface::class);
        }

        $this->review = $review;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_image_gallery()
    {
        return $this->image_gallery;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_image_gallery($image_gallery)
    {
        foreach ($image_gallery as $image) {
            if (!($image instanceof Image)) {
                throw new Invalid_Type_Exception($image, Image::class);
            }
        }

        $this->image_gallery = $image_gallery;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_products()
    {
        return $this->related_products;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_related_products($related_products)
    {
        foreach ($related_products as $related_product) {
            if (!($related_product instanceof Product_Id)) {
                throw new Invalid_Type_Exception($related_product, Product_Id::class);
            }
        }

        $this->related_products = $related_products;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_related_accessories()
    {
        return $this->related_accessories;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_related_accessories($related_accessories)
    {
        foreach ($related_accessories as $related_accessory) {
            if (!($related_accessory instanceof Product_Id)) {
                throw new Invalid_Type_Exception($related_accessory, Product_Id::class);
            }
        }

        $this->related_accessories = $related_accessories;
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function has_tags()
    {
        return !empty($this->tags);
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function get_tags()
    {
        return $this->tags;
    }

    /**
     * @inheritdoc
     * @since 0.7.1
     */
    public function set_tags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            parent::is_equal_to($object) &&
            $this->get_image_gallery() == $this->get_image_gallery();
    }
}
