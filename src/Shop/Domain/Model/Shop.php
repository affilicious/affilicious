<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Image\Image;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop extends AbstractEntity
{
	const POST_TYPE = 'shop';

	/**
	 * @var Title
	 */
	private $title;

	/**
	 * @var Image
	 */
	private $thumbnail;

    /**
     * @since 0.6
     * @param ShopId $id
     * @param Title $title
     */
	public function __construct(ShopId $id, Title $title)
	{
		$this->id = $id;
		$this->title = $title;
	}

	/**
	 * Get the shop ID
	 *
	 * @since 0.6
	 * @return ShopId
	 */
	public function getId()
	{
		return $this->id;
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
	        $this->getId()->isEqualTo($object->getId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            ($this->hasThumbnail() && $this->getThumbnail()->isEqualTo($object->getThumbnail()) || !$object->hasThumbnail());
	}
}
