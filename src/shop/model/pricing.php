<?php
namespace Affilicious\Shop\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Pricing
{
    /**
     * @var Availability
     */
    protected $availability;

    /**
     * The discounted price (sometimes called current price).
     *
     * @var null|Money
     */
    protected $discounted_price;

    /**
     * The regular stock price (sometimes called old price).
     *
     * @var null|Money
     */
    protected $stock_price;

    /**
     * Create a new available pricing with the discounted price and stock price.
     *
     * @since 0.8
     * @param Money|null $discounted_price The discounted price (sometimes called current price)
     * @param Money|null $stock_price The regular stock price (sometimes called old price)
     * @return Pricing
     */
    public static function available(Money $discounted_price = null, Money $stock_price = null)
    {
        return new self(Availability::available(), $discounted_price, $stock_price);
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
     * @param Money|null $discounted_price
     * @param Money|null $stock_price
     */
    public function __construct(Availability $availability, Money $discounted_price = null, Money $stock_price = null)
    {
        $this->availability = $availability;
        $this->discounted_price = !$availability->is_out_of_stock() ? $discounted_price : null;
        $this->stock_price = !$availability->is_out_of_stock() ? $stock_price : null;
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
    public function has_discounted_price()
    {
        return $this->discounted_price !== null;
    }

    /**
     * Get the discounted price of the pricing.
     *
     * @since 0.8
     * @return null|Money
     */
    public function get_discounted_price()
    {
        return $this->discounted_price;
    }

    /**
     * Set the discounted price of the pricing.
     *
     * @since 0.8
     * @param Money|null $discounted_price
     */
    public function set_discounted_price(Money $discounted_price = null)
    {
        $this->discounted_price = $discounted_price;
    }

    /**
     * Check if the pricing has a stock price.
     *
     * @since 0.8
     * @return bool
     */
    public function has_stock_price()
    {
        return $this->stock_price !== null;
    }

    /**
     * Get the stock price of the pricing.
     *
     * @since 0.8
     * @return null|Money
     */
    public function get_stock_price()
    {
        return $this->stock_price;
    }

    /**
     * Set the stock price of the pricing.
     *
     * @since 0.8
     * @param Money $stock_price
     */
    public function set_stock_price(Money $stock_price = null)
    {
        $this->stock_price = $stock_price;
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
            ($this->has_discounted_price() && $this->get_discounted_price()->is_equal_to($other->get_discounted_price()) || !$other->has_affiliate_id()) &&
            ($this->has_stock_price() && $this->get_stock_price()->is_equal_to($other->get_stock_price()) || !$other->has_stock_price());
    }
}
