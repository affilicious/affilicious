<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop extends AbstractEntity
{
	const POST_TYPE = 'shop';

    /**
     * @var ShopId
     */
    protected $id;

	/**
	 * @var Title
	 */
	protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

	/**
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
     * Check if the show has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the show ID
     *
     * @since 0.6
     * @return ShopId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the show ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|ShopId $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
	 * Get the title
	 *
	 * @since 0.6
	 * @return Title
	 */
	public function getTitle()
	{
		return $this->title;
	}

    /**
     * Set the title
     *
     * @since 0.6
     * @param Title $title
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

    /**
     * Get the key for database usage
     *
     * @since 0.6
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

	/**
	 * Check if the shop has a thumbnail
	 *
	 * @since 0.6
	 * @return bool
	 */
	public function hasThumbnail()
	{
		return $this->thumbnail !== null;
	}

    /**
     * Set the thumbnail image
     *
     * @since 0.6
     * @param Image $thumbnail
     */
	public function setThumbnail(Image $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

	/**
	 * Get the thumbnail image
	 *
	 * @since 0.6
	 * @return null|Image
	 */
	public function getThumbnail()
	{
		return $this->thumbnail;
	}

    /**
     * Get the raw Wordpress post
     *
     * @since 0.3
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function isEqualTo($object)
	{
		return
			$object instanceof self &&
            ($this->hasId() && $this->getId()->isEqualTo($object->getId()) || !$object->hasId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            $this->getKey()->isEqualTo($object->getKey()) &&
            ($this->hasThumbnail() && $this->getThumbnail()->isEqualTo($object->getThumbnail()) || !$object->hasThumbnail());
	}
}
