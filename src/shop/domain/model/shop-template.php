<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template extends Abstract_Entity implements Shop_Template_Interface
{
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
     * The provider for the shop updates which holds all credentials.
     *
     * @var Provider_Interface
     */
    protected $provider;

    /**
     * The date and time of the last update
     *
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @inheritdoc
     * @since 0.6
     */
	public function __construct(Title $title, Name $name, Key $key)
	{
		$this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->updated_at = new \DateTime('now');
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Shop_Template_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Shop\Domain\Model\Shop_Template_Id');
        }

        $this->id = $id;
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function get_title()
	{
		return $this->title;
	}

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_key()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function set_key(Key $key)
    {
        $this->key = $key;
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function has_thumbnail()
	{
		return $this->thumbnail !== null;
	}

    /**
     * @inheritdoc
     * @since 0.6
     */
	public function set_thumbnail($thumbnail)
    {
        if($thumbnail !== null && !($thumbnail instanceof Image)) {
            throw new Invalid_Type_Exception($thumbnail, 'Affilicious\Common\Domain\Model\Image\Image');
        }

        $this->thumbnail = $thumbnail;
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function get_thumbnail()
	{
		return $this->thumbnail;
	}

    /**
     * @inheritdoc
     * @since 0.7
     */
	public function has_provider()
    {
        return $this->provider !== null;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_provider()
    {
        return $this->provider;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_provider($provider)
    {
        if($provider !== null && !($provider instanceof Provider_Interface)) {
            throw new Invalid_Type_Exception($provider, 'Affilicious\Shop\Domain\Model\Provider\Provider_Interface');
        }

        $this->provider = $provider;
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
     * @since 0.6
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
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($object->get_thumbnail()) || !$object->has_thumbnail()) &&
            $this->get_updated_at() == $object->get_updated_at();
	}
}
