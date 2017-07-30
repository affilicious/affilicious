<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Custom_Value_Aware_Trait;
use Affilicious\Common\Model\Image;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;
use Affilicious\Provider\Model\Provider_Id;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Shop_Template
{
    use Name_Aware_Trait, Slug_Aware_Trait, Custom_Value_Aware_Trait;

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
	 * @var null|Image
	 */
	private $thumbnail;

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
     * Check if the shop template has an optional thumbnail.
     *
     * @since 0.9
     * @return bool
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the optional shop template thumbnail.
     *
     * @since 0.9
     * @return null|Image
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the optional shop template thumbnail.
     *
     * @since 0.9
     * @param null|Image $thumbnail
     */
    public function set_thumbnail(Image $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the shop template has an optional thumbnail ID.
     *
     * @deprecated 1.1 Use 'has_thumbnail' instead.
     * @since 0.8
     * @return bool
     */
    public function has_thumbnail_id()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the optional shop template thumbnail ID.
     *
     * @deprecated 1.1 Use 'get_thumbnail' instead.
     * @since 0.8
     * @return null|Image_Id
     */
    public function get_thumbnail_id()
    {
        return $this->thumbnail;
    }

    /**
     * Set the optional shop template thumbnail ID.
     *
     * @deprecated 1.1 Use 'set_thumbnail' instead.
     * @since 0.8
     * @param null|Image_Id $thumbnail_id
     */
    public function set_thumbnail_id(Image_Id $thumbnail_id = null)
    {
        if($thumbnail_id instanceof Image_Id) {
            $thumbnail_id = new Image($thumbnail_id->get_value());
        }

        $this->thumbnail = $thumbnail_id;
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
        $shop->set_template_id($this->id);
        $shop->set_thumbnail($this->thumbnail);

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
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($other->get_thumbnail()) || !$other->has_thumbnail()) &&
            ($this->has_provider_id() && $this->get_provider_id()->is_equal_to($other->get_provider_id()) || !$other->has_provider_id());
	}

    /**
     * Get the raw Wordpress term of the shop template.
     *
     * @since 0.8.2
     * @param string $output
     * @param string $filter
     * @return array|null|\WP_Error|\WP_Term
     */
	public function get_term($output = OBJECT, $filter = 'raw')
    {
        if(!$this->has_id()) {
            return null;
        }

        $term = get_term($this->id->get_value(), self::TAXONOMY, $output, $filter);

        return $term;
    }
}
