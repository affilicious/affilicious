<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Entity_Interface;
use Affilicious\Common\Domain\Model\Excerpt;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\Duplicated_Shop_Exception;
use Affilicious\Shop\Domain\Model\Affiliate_Link;
use Affilicious\Shop\Domain\Model\Shop;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Interface extends Entity_Interface
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
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Type $type
     */
    public function __construct(Title $title, Name $name, Key $key, Type $type);

    /**
     * Check if the product has an ID
     *
     * @since 0.7
     * @return bool
     */
    public function has_id();

    /**
     * Get the optional product ID
     *
     * @since 0.7
     * @return null|Product_Id
     */
    public function get_id();

    /**
     * Set the optional product ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.7
     * @param null|Product_Id $id
     */
    public function set_id($id);

    /**
     * Get the type like simple or variants.
     *
     * @since 0.7
     * @return Type
     */
    public function get_type();

    /**
     * Set the type like simple or variants
     *
     * @since 0.7
     * @param Type $type
     */
    public function set_type(Type $type);

    /**
     * Get the title
     *
     * @since 0.7
     * @return Title
     */
    public function get_title();

    /**
     * Set the title
     *
     * @since 0.7
     * @param Title $title
     */
    public function set_title(Title $title);

    /**
     * Get the name for url usage
     *
     * @since 0.7
     * @return Name
     */
    public function get_name();

    /**
     * Set the name for the url usage
     *
     * @since 0.7
     * @param Name $name
     */
    public function set_name(Name $name);

    /**
     * Get the key for database usage
     *
     * @since 0.7
     * @return Key
     */
    public function get_key();

    /**
     * Set the unique key for database usage
     *
     * @since 0.7
     * @param Key $key
     */
    public function set_key(Key $key);

    /**
     * Check if the product has any content
     *
     * @since 0.7
     * @return bool
     */
    public function has_content();

    /**
     * Get the optional content
     *
     * @since 0.7
     * @return null|Content
     */
    public function get_content();

    /**
     * Set the optional content
     *
     * @since 0.7
     * @param null|Content $content
     */
    public function set_content($content);

    /**
     * Check if the product has any excerpt
     *
     * @since 0.7
     * @return bool
     */
    public function has_excerpt();

    /**
     * Get the optional excerpt
     *
     * @since 0.7
     * @return null|Excerpt
     */
    public function get_excerpt();

    /**
     * Set the optional excerpt
     *
     * @since 0.7
     * @param Excerpt $excerpt
     */
    public function set_excerpt($excerpt);

    /**
     * Check if the product has a thumbnail
     *
     * @since 0.7
     * @return bool
     */
    public function has_thumbnail();

    /**
     * Get the thumbnail
     *
     * @since 0.7
     * @return Image
     */
    public function get_thumbnail();

    /**
     * Set the thumbnail
     *
     * @since 0.7
     * @param Image $thumbnail
     */
    public function set_thumbnail(Image $thumbnail);

    /**
     * Check if the product has a specific shop by the affiliate link
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link);

    /**
     * Add a new shop
     *
     * @since 0.7
     * @param Shop $shop
     * @throws Duplicated_Shop_Exception
     */
    public function add_shop(Shop $shop);

    /**
     * Remove a shop by the affiliate link
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link);

    /**
     * Get a shop by the name
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop
     */
    public function get_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the cheapest shop
     *
     * @since 0.7
     * @return null|Shop
     */
    public function get_cheapest_shop();

    /**
     * Get all shops
     *
     * @since 0.7
     * @return Shop[]
     */
    public function get_shops();

    /**
     * Set all shops
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.7
     * @param Shop[] $shops
     * @throws Invalid_Type_Exception
     */
    public function set_shops($shops);

    /**
     * Get the date and time of the last product update
     *
     * @since 0.7
     * @return \DateTime
     */
    public function get_updated_at();

    /**
     * Set the date and time of the last product update
     *
     * @since 0.7
     * @param \DateTime $updated_at
     */
    public function set_updated_at(\DateTime $updated_at);

    /**
     * Get the raw Wordpress post
     *
     * @since 0.7
     * @return null|\WP_Post
     */
    public function get_raw_post();
}
