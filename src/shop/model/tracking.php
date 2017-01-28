<?php
namespace Affilicious\Shop\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Tracking
{
    /**
     * @var Affiliate_Link
     */
    private $affiliate_link;

    /**
     * @var null|Affiliate_Id
     */
    private $affiliate_id;

    /**
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @param Affiliate_Id|null $affiliate_id
     */
    public function __construct(Affiliate_Link $affiliate_link, Affiliate_Id $affiliate_id = null)
    {
        $this->affiliate_link = $affiliate_link;
        $this->affiliate_id = $affiliate_id;
    }

    /**
     * Get the affiliate link of the tracking.
     *
     * @since 0.8
     * @return Affiliate_Link
     */
    public function get_affiliate_link()
    {
        return $this->affiliate_link;
    }

    /**
     * Check if the tracking has an affiliate ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_affiliate_id()
    {
        return $this->affiliate_id !== null;
    }

    /**
     * Get the affiliate ID of the tracking.
     *
     * @since 0.8
     * @return null|Affiliate_Id
     */
    public function get_affiliate_id()
    {
        return $this->affiliate_id;
    }

    /**
     * Check if this tracking is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_affiliate_link()->is_equal_to($other->get_affiliate_link()) &&
            ($this->has_affiliate_id() && $this->get_affiliate_id()->is_equal_to($other->get_affiliate_id()) || !$other->has_affiliate_id());
    }
}
