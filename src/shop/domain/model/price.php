<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Aggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Price extends Abstract_Aggregate
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @since 0.6
     * @param int|float|double|string $value
     * @param Currency $currency
     */
    public function __construct($value, Currency $currency)
    {
        if(is_string($value)) {
            $value = floatval($value);
        }
        
        if(is_numeric($value)) {
            $value = number_format($value, 2, '.', '');
        }

        if(!is_string($value)) {
            throw new Invalid_Type_Exception($value, 'string');
        }

        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * Get the price value
     *
     * @since 0.6
     * @return int
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Get the price currency
     *
     * @since 0.6
     * @return Currency
     */
    public function get_currency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_value() === $object->get_value() &&
            $this->get_currency()->is_equal_to($object->get_currency());
    }

    /**
     * Check of the given price is smaller than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.6
     * @param mixed|Price $price
     * @return bool
     */
    public function is_smaller_than($price)
    {
        return
            $price instanceof self &&
            $this->get_value() < $price->get_value() &&
            $this->get_currency()->is_equal_to($price->get_currency());
    }

    /**
     * Check of the given price is greater than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.6
     * @param mixed|Price $price
     * @return bool
     */
    public function is_greater_than($price)
    {
        return
            $price instanceof self &&
            $this->get_value() > $price->get_value() &&
            $this->get_currency()->is_equal_to($price->get_currency());
    }

    /**
     * Check of the given price is smaller than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.6
     * @param mixed|Price $price
     * @return bool
     */
    public function is_smaller_than_or_equal_to($price)
    {
        return
            $price instanceof self &&
            $this->is_smaller_than($price) ||
            $this->is_equal_to($price);
    }

    /**
     * Check of the given price is greater than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.6
     * @param mixed|Price $price
     * @return bool
     */
    public function is_greater_than_or_equal_to($price)
    {
        return
            $price instanceof self &&
            $this->is_smaller_than($price) ||
            $this->is_equal_to($price);
    }

    /**
     * Print the price
     *
     * @since 0.6
     * @return null|string
     */
    public function __toString()
    {
        $currency_symbol = $this->currency->get_symbol();
        if (empty($value) || empty($currency_symbol)) {
            return null;
        }

        $value = number_format($value, 2, '.', '');
        $price = $value . ' ' . $currency_symbol;

        return $price;
    }
}
