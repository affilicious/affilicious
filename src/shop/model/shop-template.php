<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Trait;
use Affilicious\Provider\Model\Provider_Id;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template
{
    use Name_Trait, Slug_Trait;

    /**
     * There is a limit of 20 characters for post types in Wordpress
     */
    const TAXONOMY = 'aff_shop_tmpl';

    /**
     * The unique ID of the shop template.
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var null|Shop_Template_Id
     */
    private $id;

	/**
     * The thumbnail of the shop template.
     *
	 * @var null|Image_Id
	 */
	private $thumbnail_id;

    /**
     * The provider ID for the shop updates.
     *
     * @var null|Provider_Id
     */
    private $provider_id;

    /**
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     */
	public function __construct(Name $name, Slug $slug)
	{
		$this->set_name($name);
        $this->set_slug($slug);
    }

    /**
     * Check if the shop template has an unique ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the unique ID of the shop template.
     *
     * @since 0.8
     * @return null|Shop_Template_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the unique ID of the shop template.
     *
     * @since 0.8
     * @param null|Shop_Template_Id $id
     */
    public function set_id(Shop_Template_Id $id = null)
    {
        $this->id = $id;
    }

    /**
     * Check if the shop has an optional thumbnail ID.
     *
     * @since 0.8
     * @return bool
     */
	public function has_thumbnail_id()
	{
		return $this->thumbnail_id !== null;
	}

    /**
     * Set the optional thumbnail ID.
     *
     * @since 0.8
     * @param null|Image_Id $thumbnail_id
     */
	public function set_thumbnail_id(Image_Id $thumbnail_id = null)
    {
        $this->thumbnail_id = $thumbnail_id;
    }

    /**
     * Get the optional thumbnail image
     *
     * @since 0.8
     * @return null|Image_Id
     */
	public function get_thumbnail_id()
	{
		return $this->thumbnail_id;
	}

    /**
     * Check of the shop template has an optional provider ID.
     *
     * @since 0.8
     */
	public function has_provider_id()
    {
        return $this->provider_id !== null;
    }

    /**
     * Get the optional provider ID of the shop template.
     *
     * @since 0.8
     * @return null|Provider_Id
     */
    public function get_provider_id()
    {
        return $this->provider_id;
    }

    /**
     * Set the optional provider ID of the shop template.
     *
     * @since 0.8
     * @param null|Provider_Id $provider_id
     */
    public function set_provider_id(Provider_Id $provider_id = null)
    {
        $this->provider_id = $provider_id;
    }

    /**
     * Build a new shop from the template.
     *
     * @since 0.8
     * @param Tracking $tracking
     * @param Pricing $pricing
     * @return Shop
     */
    public function build(Tracking $tracking, Pricing $pricing)
    {
        $shop = new Shop($this->name, $this->slug, $tracking, $pricing);
        $shop->set_thumbnail_id($this->thumbnail_id);

        return $shop;
    }

    /**
     * Check if this shop template is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
	public function is_equal_to($other)
	{
		return
			$other instanceof self &&
            ($this->has_id() && $this->get_id()->is_equal_to($other->get_id()) || !$other->has_id()) &&
            $this->get_name()->is_equal_to($other->get_name()) &&
            $this->get_slug()->is_equal_to($other->get_slug()) &&
            ($this->has_thumbnail_id() && $this->get_thumbnail_id()->is_equal_to($other->get_thumbnail_id()) || !$other->has_thumbnail_id()) &&
            ($this->has_provider_id() && $this->get_provider_id()->is_equal_to($other->get_provider_id()) || !$other->has_provider_id());
	}
}
