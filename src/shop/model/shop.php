<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Image\Image;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Shop
{
    /**
     * The name for display usage.
     *
     * @var Name
     */
    protected $name;

    /**
     * The optional thumbnail ID of the shop.
     *
     * @var null|Image_Id
     */
    protected $thumbnail_id;

    /**
     * The tracking contains all information to have the sale paid for the affiliate.
     *
     * @var Tracking
     */
    protected $tracking;

    /**
     * The pricing contains all information to show prices and availability.
     *
     * @var Pricing
     */
    protected $pricing;

    /**
     * The date and time of the last update.
     *
     * @var \DateTimeImmutable
     */
    protected $updated_at;

    /**
     * Create a new shop.
     *
     * @since 0.8
     * @param Name $name
     * @param Image|null $thumbnail
     * @param Tracking $tracking
     * @param Pricing $pricing
     * @return Shop
     */
    public function first_opening(Name $name, Image $thumbnail = null, Tracking $tracking, Pricing $pricing)
    {
        return new self($name, $thumbnail, $tracking, $pricing, new \DateTimeImmutable('now'));
    }

    /**
     * @since 0.8
     * @param Name $name
     * @param null|Image $thumbnail
     * @param Tracking $tracking
     * @param Pricing $pricing
     * @param \DateTimeImmutable $updated_at
     */
    public function __construct(Name $name, Image $thumbnail = null, Tracking $tracking, Pricing $pricing, \DateTimeImmutable $updated_at)
    {
        $this->name = $name;
        $this->thumbnail_id = $thumbnail;
        $this->tracking = $tracking;
        $this->pricing = $pricing;
        $this->updated_at = $updated_at;
    }

    /**
     * Get the name for display usage.
     *
     * @since 0.8
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
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
     * @since 0.8
     */
    public function has_thumbnail()
    {
        return $this->thumbnail_id !== null;
    }

    /**
     * Get the optional shop thumbnail.
     *
     * @since 0.8
     * @return null|Image
     */
    public function get_thumbnailId()
    {
        return $this->thumbnail_id;
    }

    /**
     * Get the date and time of the last update.
     *
     * @since 0.8
     * @return \DateTimeImmutable
     */
    public function get_updated_at()
    {
        return clone $this->updated_at;
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
            $this->get_tracking()->is_equal_to($other->get_tracking()) &&
            $this->get_pricing()->is_equal_to($other->get_pricing()) &&
            ($this->has_thumbnail() && $this->get_thumbnailId()->is_equal_to($other->get_thumbnailId()) || !$other->has_thumbnail()) &&
            $this->get_updated_at() == $other->get_updated_at();
    }
}
