<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Excerpt;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Product extends Abstract_Entity implements Product_Interface
{
    /**
     * The unique ID of the product.
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var Product_Id
     */
    protected $id;

    /**
     * The type of the product like simple, complex or variants.
     *
     * @var Type
     */
    protected $type;

    /**
     * The title of the product for display usage.
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the product for url usage.
     *
     * @var Name
     */
    protected $name;

    /**
     * The unique key of the product for database usage.
     *
     * @var Key
     */
    protected $key;

    /**
     * The optional content of the product.
     *
     * @var Content
     */
    protected $content;

    /**
     * The optional excerpt of the product.
     *
     * @var Excerpt
     */
    protected $excerpt;

    /**
     * The thumbnail of the product.
     *
     * @var Image
     */
    protected $thumbnail;

    /**
     * Holds the detail groups of the product.
     *
     * @var Detail_Group[]
     */
    protected $detail_groups;

    /**
     * Stores the rating in 0.5 steps from 0 to 5 and the number of votes.
     *
     * @var Review
     */
    protected $review;

    /**
     * Stores the IDs of the related products.
     *
     * @var Product_Id[]
     */
    protected $related_products;

    /**
     * Stores the IDs of the related accessories.
     *
     * @var Product_Id[]
     */
    protected $related_accessories;

    /**
     * Holds the shops like Amazon, Affilinet or Ebay.
     *
     * @var Shop[]
     */
    protected $shops;

    /**
     * The date and time of the last update.
     *
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Type $type
     */
    public function __construct(Title $title, Name $name, Key $key, Type $type)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->type = $type;
        $this->shops = array();
        $this->updated_at = new \DateTime('now');
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Product_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Product\Domain\Model\Product_Id');
        }

        $this->id = $id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_title(Title $title)
    {
        $this->title = $title;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_key(Key $key)
    {
        $this->key = $key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_content()
    {
        return $this->content !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_content($content)
    {
        if($content !== null && !($content instanceof Content)) {
            throw new Invalid_Type_Exception($content, 'Affilicious\Common\Domain\Model\Content');
        }

        $this->content = $content;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_excerpt()
    {
        return $this->excerpt !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_excerpt()
    {
        return $this->excerpt;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_excerpt($excerpt)
    {
        if($excerpt !== null && !($excerpt instanceof Excerpt)) {
            throw new Invalid_Type_Exception($excerpt, 'Affilicious\Common\Domain\Model\Excerpt');
        }

        $this->excerpt = $excerpt;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     */
    public function set_thumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof Image)) {
            throw new Invalid_Type_Exception($thumbnail, 'Affilicious\Common\Domain\Model\Image');
        }

        $this->thumbnail = $thumbnail;
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
        if($review !== null && !($review instanceof Review)) {
            throw new Invalid_Type_Exception($review, 'Affilicious\Product\Domain\Model\Review\Review');
        }

        $this->review = $review;
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
                throw new Invalid_Type_Exception($related_product, 'Affilicious\Product\Domain\Model\Product_Id');
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
                throw new Invalid_Type_Exception($related_accessory, 'Affilicious\Product\Domain\Model\Product_Id');
            }
        }

        $this->related_accessories = $related_accessories;
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
        /** @var Shop $cheapest_shop */
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

        // add_shop checks for the type
        foreach ($shops as $shop) {
            $this->add_shop($shop);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_updated_at()
    {
        return clone $this->updated_at;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_updated_at(\DateTime $updated_at)
    {
        $this->updated_at = clone $updated_at;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_raw_post()
    {
        if(!$this->has_id()) {
            return null;
        }

        return get_post($this->id->get_value());
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof static &&
            ($this->has_id() && $this->get_id()->is_equal_to($object->get_id()) || !$object->has_id()) &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key()) &&
            $this->get_type()->is_equal_to($object->get_type()) &&
            $this->get_content()->is_equal_to($object->get_content()) &&
            $this->get_excerpt()->is_equal_to($object->get_excerpt()) &&
            $this->get_thumbnail()->is_equal_to($object->get_thumbnail()) &&
            $this->get_detail_groups() == $object->get_detail_groups() &&
            ($this->has_review() && $this->get_review()->is_equal_to($object->get_review()) || !$object->has_review()) &&
            $this->get_related_products() == $object->get_related_products() &&
            $this->get_related_accessories() == $object->get_related_accessories() &&
            $this->get_shops() == $object->get_shops();
    }
}
