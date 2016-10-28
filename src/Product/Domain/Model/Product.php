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
use Affilicious\Product\Domain\Exception\Duplicated_Detail_Group_Exception;
use Affilicious\Product\Domain\Exception\Duplicated_Shop_Exception;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Variant\Product_Variant;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product extends Abstract_Entity
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     * TODO: Change the post type to 'aff_product' before the beta release
     */
    const POST_TYPE = 'product';

    /**
     * The default slug is in English but can be translated in the settings
     */
    const SLUG = 'product';

    /**
     * The unique ID of the product
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var Product_Id
     */
    protected $id;

    /**
     * The type of the product like simple or variants
     *
     * @var Type
     */
    protected $type;

    /**
     * The title of the product for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the product for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The unique key of the product for database usage
     *
     * @var Key
     */
    protected $key;

    /**
     * The optional content of the product
     *
     * @var Content
     */
    protected $content;

    /**
     * The optional excerpt of the product
     *
     * @var Excerpt
     */
    protected $excerpt;

    /**
     * The thumbnail of the product
     *
     * @var Image
     */
    protected $thumbnail;

    /**
     * Holds the shops like Amazon, Affilinet or Ebay.
     *
     * @var Shop[]
     */
    protected $shops;

    /**
     * Holds all product variants of the product
     *
     * @var Product_Variant[]
     */
    protected $variants;

    /**
     * Holds the detail groups of the product
     *
     * @var Detail_group[]
     */
    protected $detail_groups;

    /**
     * Stores the rating in 0.5 steps from 0 to 5 and the number of votes
     *
     * @var Review
     */
    protected $review;

    /**
     * Stores the IDs of the related products
     *
     * @var int[]
     */
    protected $related_products;

    /**
     * Stores the IDs of the related accessories
     *
     * @var int[]
     */
    protected $related_accessories;

    /**
     * Stores the IDs of the image gallery attachments
     *
     * @var int[]
     */
    protected $image_gallery;

    /**
     * @since 0.6
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
        $this->variants = array();
        $this->detail_groups = array();
        $this->related_products = array();
        $this->related_accessories = array();
        $this->image_gallery = array();
    }

    /**
     * Check if the product has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the optional product ID
     *
     * @since 0.6
     * @return null|Product_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the optional product ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|Product_Id $id
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Product_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Product\Domain\Model\Product_Id');
        }

        $this->id = $id;
    }

    /**
     * Get the type like simple or variants.
     *
     * @since 0.6
     * @return Type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Set the type like simple or variants
     *
     * @since 0.6
     * @param Type $type
     */
    public function set_type(Type $type)
    {
        $this->type = $type;
    }

    /**
     * Get the title
     *
     * @since 0.6
     * @return Title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Set the title
     *
     * @since 0.6
     * @param Title $title
     */
    public function set_title(Title $title)
    {
        $this->title = $title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Set the name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * Set the unique key for database usage
     *
     * @since 0.6
     * @param Key $key
     */
    public function set_key(Key $key)
    {
        $this->key = $key;
    }

    /**
     * Check if the product has any content
     *
     * @since 0.6
     * @return bool
     */
    public function has_content()
    {
        return $this->content !== null;
    }

    /**
     * Get the optional content
     *
     * @since 0.6
     * @return null|Content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * Set the optional content
     *
     * @since 0.6
     * @param null|Content $content
     */
    public function set_content($content)
    {
        if($content !== null && !($content instanceof Content)) {
            throw new Invalid_Type_Exception($content, 'Affilicious\Common\Domain\Model\Content');
        }

        $this->content = $content;
    }

    /**
     * Check if the product has any excerpt
     *
     * @since 0.6
     * @return bool
     */
    public function has_excerpt()
    {
        return $this->excerpt !== null;
    }

    /**
     * Get the optional excerpt
     *
     * @since 0.6
     * @return null|Excerpt
     */
    public function get_excerpt()
    {
        return $this->excerpt;
    }

    /**
     * Set the optional excerpt
     *
     * @since 0.6
     * @param Excerpt $excerpt
     */
    public function set_excerpt($excerpt)
    {
        if(!($excerpt instanceof Excerpt)) {
            throw new Invalid_Type_Exception($excerpt, 'Affilicious\Common\Domain\Model\Excerpt');
        }

        $this->excerpt = $excerpt;
    }

    /**
     * Check if the product has a thumbnail
     *
     * @since 0.6
     * @return bool
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the thumbnail
     *
     * @since 0.6
     * @return Image
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the thumbnail
     *
     * @since 0.6
     * @param Image $thumbnail
     */
    public function set_thumbnail(Image $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the product has a specific shop by the affiliate link
     *
     * @since 0.6
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link)
    {
        return isset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * Add a new shop
     *
     * @since 0.6
     * @param Shop $shop
     * @throws Duplicated_Shop_Exception
     */
    public function add_shop(Shop $shop)
    {
        /*if($this->has_shop($shop->get_name())) {
            throw new Duplicated_Shop_Exception($shop, $this);
        }*/

        $this->shops[$shop->get_affiliate_link()->get_value()] = $shop;
    }

    /**
     * Remove a shop by the affiliate link
     *
     * @since 0.6
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link)
    {
        unset($this->shops[$affiliate_link->get_value()]);
    }

    /**
     * Get a shop by the name
     *
     * @since 0.6
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop
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
     * Get the cheapest shop
     *
     * @since 0.6
     * @return null|Shop
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
     * Get all shops
     *
     * @since 0.6
     * @return Shop[]
     */
    public function get_shops()
    {
        $shops = array_values($this->shops);

        return $shops;
    }

    /**
     * Set all shops
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.6
     * @param Shop[] $shops
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
     * Check if the product has a specific variant by the name
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function has_variant(Name $name)
    {
        return isset($this->variants[$name->get_value()]);
    }

    /**
     * Add a new product variant
     *
     * @since 0.6
     * @param Product_Variant $variant
     */
    public function add_variant(Product_Variant $variant)
    {
        /*if($this->has_variant($variant->get_name())) {
            throw new Duplicated_Variant_Exception($variant, $this);
        }*/

        $this->variants[$variant->get_name()->get_value()] = $variant;
    }

    /**
     * Remove an existing product variant by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function remove_variant(Name $name)
    {
        unset($this->variants[$name->get_value()]);
    }

    /**
     * Get the product variant by the name
     *
     * @since 0.6
     * @param Name $name
     * @return null|Product_Variant
     */
    public function get_variant(Name $name)
    {
        if(!$this->has_variant($name)) {
            return null;
        }

        $variant = $this->variants[$name->get_value()];

        return $variant;
    }

    /**
     * Get the default variant
     *
     * @since 0.6
     * @return Product_Variant|mixed|null
     */
    public function get_default_variant()
    {
        foreach ($this->variants as $variant) {
            if($variant->is_default()) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * Get all product variants
     *
     * @since 0.6
     * @return Product_Variant[]
     */
    public function get_variants()
    {
        $variants = array_values($this->variants);

        return $variants;
    }

    /**
     * Set all product variants
     * If you do this, the old product variants going to be replaced.
     *
     * @since 0.6
     * @param Product_Variant[] $variants
     * @throws Invalid_Type_Exception
     */
    public function set_variants($variants)
    {
        $this->variants = array();

        // add_variant checks for the type
        foreach ($variants as $variant) {
            $this->add_variant($variant);
        }
    }

    /**
     * Check if the product has a specific detail group
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function has_detail_group(Name $name)
    {
        return isset($this->detail_groups[$name->get_value()]);
    }

    /**
     * Add a new detail group
     *
     * @since 0.6
     * @param Detail_Group $detail_group
     * @throws Duplicated_Detail_Group_Exception
     */
    public function add_detail_group(Detail_Group $detail_group)
    {
        /*if($this->has_detail_group($detail_group->get_name())) {
            throw new Duplicated_Detail_Group_Exception($detail_group, $this);
        }*/

        $this->detail_groups[$detail_group->get_name()->get_value()] = $detail_group;
    }

    /**
     * Remove a detail group by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function remove_detail_group(Name $name)
    {
        unset($this->detail_groups[$name->get_value()]);
    }

    /**
     * Get a detail group by the name
     *
     * @since 0.6
     * @param Name $name
     * @return null|Detail_group
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
     * Get all detail groups
     *
     * @since 0.6
     * @return Detail_Group[]
     */
    public function get_detail_groups()
    {
        $detail_groups = array_values($this->detail_groups);

        return $detail_groups;
    }

    /**
     * Set all detail groups
     * If you do this, the old detail groups going to be replaced.
     *
     * @since 0.6
     * @param Detail_group[] $detail_groups
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
     * Check if the product has a review
     *
     * @since 0.6
     * @return bool
     */
    public function has_review()
    {
        return $this->review !== null;
    }

    /**
     * Get the optional review
     *
     * @since 0.6
     * @return null|Review
     */
    public function get_review()
    {
        return $this->review;
    }

    /**
     * Set the optional review
     *
     * @since 0.6
     * @param null|Review $review
     */
    public function set_review($review)
    {
        if($review !== null && !($review instanceof Review)) {
            throw new Invalid_Type_Exception($review, 'Affilicious\Product\Domain\Model\Review\Review');
        }

        $this->review = $review;
    }

    /**
     * Get the IDs of all related products
     *
     * @since 0.6
     * @return Product_Id[]
     */
    public function get_related_products()
    {
        return $this->related_products;
    }

    /**
     * Set the IDs of all related products
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param Product_Id[] $related_products
     * @throws Invalid_Type_Exception
     */
    public function set_related_products($related_products)
    {
        foreach ($related_products as $related_product) {
            if (!($related_product instanceof Product_Id)) {
                throw new Invalid_Type_Exception($related_product, get_class(new Product_Id(0)));
            }
        }

        $this->related_products = $related_products;
    }

    /**
     * Get the IDs of all related accessories
     *
     * @since 0.6
     * @return Product_Id[]
     */
    public function get_related_accessories()
    {
        return $this->related_accessories;
    }

    /**
     * Set the IDs of all related accessories
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param Product_Id[] $related_accessories
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
     * Get the IDs of the media attachments for the image gallery
     *
     * @since 0.6
     * @return Image[]
     */
    public function get_image_gallery()
    {
        return $this->image_gallery;
    }

    /**
     * Set the IDs of the media attachments for the image gallery
     * If you do this, the old images going to be replaced.
     *
     * @since 0.6
     * @param Image[] $image_gallery
     * @throws Invalid_Type_Exception
     */
    public function set_image_gallery($image_gallery)
    {
        foreach ($image_gallery as $image) {
            if (!($image instanceof Image)) {
                throw new Invalid_Type_Exception($image, 'Affilicious\Common\Domain\Model\Image\Image');
            }
        }

        $this->image_gallery = $image_gallery;
    }

    /**
     * Get the raw Wordpress post
     *
     * @since 0.6
     * @return null|\WP_Post
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
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            ($this->has_id() && $this->get_id()->is_equal_to($object->get_id()) || !$object->has_id()) &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key());
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
