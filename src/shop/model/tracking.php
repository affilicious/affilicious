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
     * @var null|Affiliate_Product_Id
     */
    private $affiliate_product_id;

    /**
     * @since 0.8
     * @param Affiliate_Link $affiliate_link
     * @param null|Affiliate_Product_Id $affiliate_product_id
     */
    public function __construct(Affiliate_Link $affiliate_link, Affiliate_Product_Id $affiliate_product_id = null)
    {
        $this->affiliate_link = $affiliate_link;
        $this->affiliate_product_id = $affiliate_product_id;
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
	 * Set the affiliate link of the tracking.
	 *
	 * @since 0.9.8
	 * @param Affiliate_Link $affiliate_link
	 */
    public function set_affiliate_link(Affiliate_Link $affiliate_link)
    {
    	$this->affiliate_link = $affiliate_link;
    }

    /**
     * Check if the tracking has an affiliate product ID.
     *
     * @since 0.9
     * @return bool
     */
    public function has_affiliate_product_id()
    {
        return $this->affiliate_product_id !== null;
    }

    /**
     * Get the affiliate product ID of the tracking.
     *
     * @since 0.9
     * @return null|Affiliate_Product_Id
     */
    public function get_affiliate_product_id()
    {
        return $this->affiliate_product_id;
    }

	/**
	 * Set the affiliate product ID of the tracking.
	 *
	 * @since 0.9.8
	 * @param Affiliate_Product_Id $affiliate_product_id
	 */
    public function set_affiliate_product_id(Affiliate_Product_Id $affiliate_product_id)
    {
    	$this->affiliate_product_id = $affiliate_product_id;
    }

    /**
     * Check if the tracking has an affiliate ID.
     *
     * @deprecated 1.0 Use 'has_affiliate_product_id' instead.
     * @since 0.8
     * @return bool
     */
    public function has_affiliate_id()
    {
        return $this->has_affiliate_product_id();
    }

    /**
     * Get the affiliate ID of the tracking.
     *
     * @deprecated 1.0 Use 'get_affiliate_product_id' instead.
     * @since 0.8
     * @return null|Affiliate_Product_Id
     */
    public function get_affiliate_id()
    {
        return $this->get_affiliate_product_id();
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
            ($this->has_affiliate_product_id() && $this->get_affiliate_product_id()->is_equal_to($other->get_affiliate_product_id()) || !$other->has_affiliate_product_id());
    }
}
