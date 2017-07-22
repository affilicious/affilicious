<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Image;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop
{
    use Name_Aware_Trait, Slug_Aware_Trait {
        Name_Aware_Trait::set_name as private;
        Slug_Aware_Trait::set_slug as private;
    }

    /**
     * The tracking contains all information to have the sale paid for the affiliate.
     *
     * @var Tracking
     */
    private $tracking;

    /**
     * The pricing contains all information to show prices and availability.
     *
     * @var Pricing
     */
    private $pricing;

    /**
     * The optional thumbnail of the shop.
     *
     * @var null|Image
     */
    private $thumbnail;

    /**
     * The optional shop template ID.
     *
     * @var Shop_Template_Id
     */
    private $template_id;

    /**
     * The date and time of the last update.
     *
     * @var \DateTimeImmutable
     */
    private $updated_at;

    /**
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Tracking $tracking
     * @param Pricing $pricing
     */
    public function __construct(Name $name, Slug $slug, Tracking $tracking, Pricing $pricing)
    {
        $this->set_name($name);
        $this->set_slug($slug);
        $this->tracking = $tracking;
        $this->pricing = $pricing;
        $this->updated_at = new \DateTimeImmutable('now');
    }

    /**
     * Get the tracking which contains all information to have the sale paid for the affiliate.
     *
     * @since 0.8
     * @return Tracking
     */
    public function get_tracking()
    {
        return $this->tracking;
    }

    /**
     * Get the pricing which contains all information to show prices and availability.
     *
     * @since 0.8
     * @return Pricing
     */
    public function get_pricing()
    {
        return $this->pricing;
    }

    /**
     * Check if the shop has an optional thumbnail.
     *
     * @since 0.9
     * @return bool
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the optional shop thumbnail.
     *
     * @since 0.9
     * @return null|Image
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the optional shop thumbnail.
     *
     * @since 0.9
     * @param null|Image $thumbnail
     */
    public function set_thumbnail(Image $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the shop has an optional thumbnail ID.
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
     * Get the optional shop thumbnail ID.
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
     * Set the optional shop thumbnail ID.
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
     * Check if the attribute has an optional template ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_template_id()
    {
        return $this->template_id !== null;
    }

    /**
     * Get the optional attribute template ID.
     *
     * @since 0.8
     * @return null|Shop_Template_Id
     */
    public function get_template_id()
    {
        return $this->template_id;
    }

    /**
     * Set the optional attribute template ID.
     *
     * @since 0.8
     * @param null|Shop_Template_Id $template_id
     */
    public function set_template_id(Shop_Template_Id $template_id = null)
    {
        $this->template_id = $template_id;
    }

    /**
     * Get the date and time of the last update.
     *
     * @since 0.8
     * @return \DateTimeImmutable
     */
    public function get_updated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the date and time of the last update.
     *
     * @since 0.8
     * @param \DateTimeImmutable $updated_at
     */
    public function set_updated_at(\DateTimeImmutable $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Check if the other shop is cheaper than the current one.
     * If both shops haven't got a discounted price, this method will return true.
     *
     * @since 0.8
     * @param Shop $other_shop
     * @return bool
     */
    public function is_cheaper_than(Shop $other_shop)
    {
        $other_pricing = $other_shop->get_pricing();

        if(!$this->pricing->has_price() && !$other_pricing->has_price()) {
            return true;
        }

        if($this->pricing->has_price() && !$other_pricing->has_price()) {
            return true;
        }

        if(!$this->pricing->has_price() && $other_pricing->has_price()) {
            return false;
        }

        return $this->pricing->get_price()->is_smaller_than($other_pricing->get_price());
    }

    /**
     * Check if this shop is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_name()->is_equal_to($other->get_name()) &&
            $this->get_slug()->is_equal_to($other->get_slug()) &&
            $this->get_tracking()->is_equal_to($other->get_tracking()) &&
            $this->get_pricing()->is_equal_to($other->get_pricing()) &&
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($other->get_thumbnail()) || !$other->has_thumbnail()) &&
            ($this->has_template_id() && $this->get_template_id()->is_equal_to($other->get_template_id()) || !$other->has_template_id()) &&
            $this->get_updated_at() === $other->get_updated_at();
    }
}
