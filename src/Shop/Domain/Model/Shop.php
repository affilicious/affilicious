<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Model\AbstractEntity;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop extends AbstractEntity
{
	const POST_TYPE = 'shop';

	/**
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * @var Title
	 */
	private $title;

	/**
	 * @var Logo
	 */
	private $logo;

	/**
	 * @since 0.3
	 *
	 * @param \WP_Post $post
	 */
	public function __construct(\WP_Post $post)
	{
		$this->post = $post;
		$this->id = new ShopId($this->post->ID);
		$this->title = new Title($this->post->post_title);

		if($logoId = get_post_thumbnail_id()) {
			$logoSrc = wp_get_attachment_image_src($logoId, 'featured_preview');
			$logoSrc = $logoSrc[0];
			$this->logo = new Logo($logoSrc);
		}
	}

	/**
	 * Get the shop ID
	 *
	 * @since 0.5.2
	 * @return ShopId
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the title
	 *
	 * @since 0.5.2
	 * @return Title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Check if the shop has a logo
	 *
	 * @since 0.3
	 * @return bool
	 */
	public function hasLogo()
	{
		return $this->logo === null;
	}

	/**
	 * Get the shop logo
	 *
	 * @since 0.5.2
	 * @return null|Logo
	 */
	public function getLogo()
	{
		return $this->logo;
	}

	/**
	 * Get the raw Wordpress post
	 *
	 * @since 0.3
	 * @return \WP_Post
	 */
	public function getRawPost()
	{
		return $this->post;
	}

	/**
	 * @inheritdoc
	 * @since 0.5.2
	 */
	public function isEqualTo($object)
	{
		return
			$object instanceof self &&
	        $this->getId()->isEqualTo($object->getId());
	}
}
