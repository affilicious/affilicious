<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template extends Abstract_Entity
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     * TODO: _change the post type to 'aff_shop_template' before the beta release
     */
	const POST_TYPE = 'shop';

    /**
     * The unique ID of the shop template
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var Shop_Template_Id
     */
    protected $id;

	/**
     * The title of the shop template for display usage
     *
	 * @var Title
	 */
	protected $title;

    /**
     * The unique name of the shop template for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the shop template for database usage
     *
     * @var Key
     */
    protected $key;

	/**
     * The thumbnail/logo of the shop template
     *
	 * @var Image
	 */
	protected $thumbnail;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
	public function __construct(Title $title, Name $name, Key $key)
	{
		$this->title = $title;
        $this->name = $name;
        $this->key = $key;
    }

    /**
     * Check if the shop template has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the shop template ID
     *
     * @since 0.6
     * @return Shop_Template_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the optional shop template ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|Shop_Template_Id $id
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Shop_Template_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Shop\Domain\Model\Shop_Template_Id');
        }

        $this->id = $id;
    }

	/**
	 * Get the title for display usage
	 *
	 * @since 0.6
	 * @return Title
	 */
	public function get_title()
	{
		return $this->title;
	}

    /**
     * Get the unique name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Set the unique name for url usage
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
	 * Check if the shop has an optional thumbnail
	 *
	 * @since 0.6
	 * @return bool
	 */
	public function has_thumbnail()
	{
		return $this->thumbnail !== null;
	}

    /**
     * Set the optional thumbnail image
     *
     * @since 0.6
     * @param null|Image $thumbnail
     */
	public function set_thumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof Image)) {
            throw new Invalid_Type_Exception($thumbnail, 'Affilicious\Common\Domain\Model\Image\Image');
        }

        $this->thumbnail = $thumbnail;
    }

	/**
	 * Get the optional thumbnail image
	 *
	 * @since 0.6
	 * @return null|Image
	 */
	public function get_thumbnail()
	{
		return $this->thumbnail;
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
            $this->get_key()->is_equal_to($object->get_key()) &&
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($object->get_thumbnail()) || !$object->has_thumbnail());
	}
}
