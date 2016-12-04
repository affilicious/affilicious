<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\Entity_Interface;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Domain\Model\Update_Aware_Interface;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Shop_Template_Interface extends Entity_Interface, Update_Aware_Interface
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     */
    const POST_TYPE = 'aff_shop_template';

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key);

    /**
     * Check if the shop template has an optional ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id();

    /**
     * Get the optional shop template ID
     *
     * @since 0.6
     * @return null|Shop_Template_Id
     */
    public function get_id();

    /**
     * Set the optional shop template ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|Shop_Template_Id $id
     */
    public function set_id($id);

    /**
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function get_title();

    /**
     * Get the unique name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name();

    /**
     * Set the unique name for url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function set_name(Name $name);

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function get_key();

    /**
     * Set the unique key for database usage
     *
     * @since 0.6
     * @param Key $key
     */
    public function set_key(Key $key);

    /**
     * Check if the shop has an optional thumbnail
     *
     * @since 0.6
     * @return bool
     */
    public function has_thumbnail();

    /**
     * Get the optional thumbnail image
     *
     * @since 0.6
     * @return null|Image
     */
    public function get_thumbnail();

    /**
     * Set the optional thumbnail image
     *
     * @since 0.6
     * @param null|Image $thumbnail
     */
    public function set_thumbnail($thumbnail);

    /**
     * Check if the shop template has an optional provider.
     *
     * @since 0.7
     * @return bool
     */
    public function has_provider();

    /**
     * Get the optional provider of the shop template
     *
     * @since 0.7
     * @return null|Provider_Interface
     */
    public function get_provider();

    /**
     * Set the optional provider of the shop template
     *
     * @since 0.7
     * @param null|Provider_Interface $provider
     */
    public function set_provider($provider);

    /**
     * Get the raw Wordpress post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function get_raw_post();
}
