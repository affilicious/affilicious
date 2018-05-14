<?php
namespace Affilicious\Shop\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Pricing
{
    /**
     * @since 0.8
     * @var Availability
     */
    protected $availability;

    /**
     * The discounted price (sometimes called current price).
     *
     * @since 0.8
     * @var null|Money
     */
    protected $price;

    /**
     * The regular stock price (sometimes called old price).
     *
     * @since 0.8
     * @var null|Money
     */
    protected $old_price;

    /**
     * Create a new available pricing with the discounted price and stock price.
     *
     * @since 0.8
     * @param Money|null $price The discounted price (sometimes called current price)
     * @param Money|null $old_price The regular stock price (sometimes called old price)
     * @return Pricing
     */
    public static function available(Money $price = null, Money $old_price = null)
    {
        return new self(Availability::available(), $price, $old_price);
    }

    /**
     * Create a new out of stock pricing.
     *
     * @since 0.8
     * @return Pricing
     */
    public static function out_of_stock()
    {
        return new self(Availability::out_of_stock(), null, null);
    }

    /**
     * The discounted and stock price will be removed, if the availability is out of stock.
     *
     * @since 0.8
     * @param Availability $availability
     * @param Money|null $price
     * @param Money|null $old_price
     */
    public function __construct(Availability $availability, Money $price = null, Money $old_price = null)
    {
        $this->availability = $availability;
        $this->price = !$availability->is_out_of_stock() ? $price : null;
        $this->old_price = !$availability->is_out_of_stock() ? $old_price : null;
    }

    /**
     * Get the availability of the pricing.
     *
     * @since 0.8
     * @return Availability
     */
    public function get_availability()
    {
        return $this->availability;
    }

    /**
     * Set the availability of the pricing.
     *
     * @since 0.8
     * @param Availability $availability
     */
    public function set_availability(Availability $availability)
    {
        $this->availability = $availability;
    }

    /**
     * Check if the pricing has a discounted price.
     *
     * @since 0.8
     * @return bool
     */
    public function has_price()
    {
        return $this->price !== null;
    }

    /**
     * Get the discounted price of the pricing.
     *
     * @since 0.8
     * @return null|Money
     */
    public function get_price()
    {
        return $this->price;
    }

    /**
     * Set the discounted price of the pricing.
     *
     * @since 0.8
     * @param Money|null $price
     */
    public function set_price(Money $price = null)
    {
        $this->price = $price;
    }

    /**
     * Check if the pricing has a stock price.
     *
     * @since 0.8
     * @return bool
     */
    public function has_old_price()
    {
        return $this->old_price !== null;
    }

    /**
     * Get the stock price of the pricing.
     *
     * @since 0.8
     * @return null|Money
     */
    public function get_old_price()
    {
        return $this->old_price;
    }

    /**
     * Set the stock price of the pricing.
     *
     * @since 0.8
     * @param Money $old_price
     */
    public function set_old_price(Money $old_price = null)
    {
        $this->old_price = $old_price;
    }

    /**
     * Check if this pricing is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_availability()->is_equal_to($other->get_availability()) &&
            ($this->has_price() && $this->get_price()->is_equal_to($other->get_price()) || !$other->has_affiliate_product_id()) &&
            ($this->has_old_price() && $this->get_old_price()->is_equal_to($other->get_old_price()) || !$other->has_old_price());
    }
}
