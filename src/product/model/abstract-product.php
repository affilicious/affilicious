<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Exception\Invalid_Type_Exception;
use Affilicious\Common\Model\Abstract_Entity;
use Affilicious\Common\Model\Content;
use Affilicious\Common\Model\Excerpt;
use Affilicious\Common\Model\Image\Image;
use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Detail\Model\Detail_Group;
use Affilicious\Product\Model\Review\Review;
use Affilicious\Shop\Model\Shop;

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
     * @var Name
     */
    protected $title;

    /**
     * The unique name of the product for url usage.
     *
     * @var Slug
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
     * @var \DateTimeImmutable
     */
    protected $updated_at;

    /**
     * @since 0.7
     * @param Name $title
     * @param Slug $name
     * @param Key $key
     * @param Type $type
     */
    public function __construct(Name $title, Slug $name, Key $key, Type $type)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->type = $type;
        $this->shops = array();
        $this->updated_at = new \DateTimeImmutable('now');
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
            throw new Invalid_Type_Exception($id, Product_Id::class);
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
    public function set_title(Name $title)
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
    public function set_name(Slug $name)
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
            throw new Invalid_Type_Exception($content, Content::class);
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
            throw new Invalid_Type_Exception($excerpt, Excerpt::class);
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
            throw new Invalid_Type_Exception($thumbnail, Image::class);
        }

        $this->thumbnail = $thumbnail;
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
    public function set_updated_at(\DateTimeImmutable $updated_at)
    {
        $this->updated_at = $updated_at;
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
            $this->get_shops() == $object->get_shops() &&
            $this->get_updated_at() == $object->get_updated_at();
    }
}
