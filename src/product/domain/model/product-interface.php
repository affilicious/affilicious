<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Entity_Interface;
use Affilicious\Common\Domain\Model\Excerpt;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Domain\Model\Updateable_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Product_Interface extends Entity_Interface, Updateable_Interface
{
    /**
     * There is a limit of 20 characters for post types in Wordpress.
     */
    const POST_TYPE = 'aff_product';

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
     * Get the raw Wordpress post
     *
     * @since 0.7
     * @return null|\WP_Post
     */
    public function get_raw_post();
}
