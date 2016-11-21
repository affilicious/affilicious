<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Entity_Interface;
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

interface Product_Interface extends Entity_Interface
{
    /**
     * There is a limit of 20 characters for post types in Wordpress.
     * TODO: Change the post type to 'aff_product' before the beta release
     */
    const POST_TYPE = 'product';

    /**
     * The default slug is in English but can be translated in the options.
     */
    const SLUG = 'product';

    /**
     * Check if the product has an optional ID.
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
     * Set the optional product ID.
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.7
     * @param null|Product_Id $id
     */
    public function set_id($id);

    /**
     * Get the type like simple, complex or variants.
     *
     * @since 0.7
     * @return Type
     */
    public function get_type();

    /**
     * Get the title for display usage.
     *
     * @since 0.7
     * @return Title
     */
    public function get_title();

    /**
     * Set the title for display usage.
     *
     * @since 0.7
     * @param Title $title
     */
    public function set_title(Title $title);

    /**
     * Get the name for url usage.
     *
     * @since 0.7
     * @return Name
     */
    public function get_name();

    /**
     * Set the name for the url usage.
     *
     * @since 0.7
     * @param Name $name
     */
    public function set_name(Name $name);

    /**
     * Get the key for database usage.
     *
     * @since 0.7
     * @return Key
     */
    public function get_key();

    /**
     * Set the unique key for database usage.
     *
     * @since 0.7
     * @param Key $key
     */
    public function set_key(Key $key);

    /**
     * Check if the product has any content.
     *
     * @since 0.7
     * @return bool
     */
    public function has_content();

    /**
     * Get the optional content.
     *
     * @since 0.7
     * @return null|Content
     */
    public function get_content();

    /**
     * Set the optional content.
     *
     * @since 0.7
     * @param null|Content $content
     */
    public function set_content($content);

    /**
     * Check if the product has any excerpt.
     *
     * @since 0.7
     * @return bool
     */
    public function has_excerpt();

    /**
     * Get the optional excerpt.
     *
     * @since 0.7
     * @return null|Excerpt
     */
    public function get_excerpt();

    /**
     * Set the optional excerpt.
     *
     * @since 0.7
     * @param null|Excerpt $excerpt
     */
    public function set_excerpt($excerpt);

    /**
     * Check if the product has a thumbnail.
     *
     * @since 0.7
     * @return bool
     */
    public function has_thumbnail();

    /**
     * Get the thumbnail.
     *
     * @since 0.7
     * @return Image
     */
    public function get_thumbnail();

    /**
     * Set the thumbnail.
     *
     * @since 0.7
     * @param null|Image $thumbnail
     */
    public function set_thumbnail($thumbnail);

    /**
     * Check if the product has a specific detail group.
     *
     * @since 0.7
     * @param Name $name
     * @return bool
     */
    public function has_detail_group(Name $name);

    /**
     * Add a new detail group.
     *
     * @since 0.7
     * @param Detail_Group $detail_group
     */
    public function add_detail_group(Detail_Group $detail_group);

    /**
     * Remove a detail group by the name.
     *
     * @since 0.7
     * @param Name $name
     */
    public function remove_detail_group(Name $name);

    /**
     * Get a detail group by the name.
     *
     * @since 0.7
     * @param Name $name
     * @return null|Detail_Group
     */
    public function get_detail_group(Name $name);

    /**
     * Get all detail groups.
     *
     * @since 0.7
     * @return Detail_Group[]
     */
    public function get_detail_groups();

    /**
     * Set all detail groups.
     * If you do this, the old detail groups going to be replaced.
     *
     * @since 0.7
     * @param Detail_Group[] $detail_groups
     */
    public function set_detail_groups($detail_groups);

    /**
     * Check if the product has a review.
     *
     * @since 0.7
     * @return bool
     */
    public function has_review();

    /**
     * Get the optional review.
     *
     * @since 0.7
     * @return null|Review
     */
    public function get_review();

    /**
     * Set the optional review.
     *
     * @since 0.7
     * @param null|Review $review
     */
    public function set_review($review);

    /**
     * Get the IDs of all related products.
     *
     * @since 0.7
     * @return Product_Id[]
     */
    public function get_related_products();

    /**
     * Set the IDs of all related products.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.7
     * @param Product_Id[] $related_products
     */
    public function set_related_products($related_products);

    /**
     * Get the IDs of all related accessories.
     *
     * @since 0.7
     * @return Product_Id[]
     */
    public function get_related_accessories();

    /**
     * Set the IDs of all related accessories.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.7
     * @param Product_Id[] $related_accessories
     */
    public function set_related_accessories($related_accessories);

    /**
     * Check if the product has a specific shop by the affiliate link.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return bool
     */
    public function has_shop(Affiliate_Link $affiliate_link);

    /**
     * Add a new shop.
     *
     * @since 0.7
     * @param Shop_Interface $shop
     */
    public function add_shop(Shop_Interface $shop);

    /**
     * Remove THE shop by the affiliate link.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     */
    public function remove_shop(Affiliate_Link $affiliate_link);

    /**
     * Get THE shop by the name.
     *
     * @since 0.7
     * @param Affiliate_Link $affiliate_link
     * @return null|Shop_Interface
     */
    public function get_shop(Affiliate_Link $affiliate_link);

    /**
     * Get the cheapest shop.
     *
     * @since 0.7
     * @return null|Shop_Interface
     */
    public function get_cheapest_shop();

    /**
     * Get all shops.
     *
     * @since 0.7
     * @return Shop_Interface[]
     */
    public function get_shops();

    /**
     * Set all shops
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.7
     * @param Shop[] $shops
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
