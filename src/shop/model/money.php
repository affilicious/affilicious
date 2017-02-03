<?php
namespace Affilicious\Shop\Model;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Money
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
     * Create a new free of charge money with the currency.
     *
     * @since 0.8
     * @param Currency $currency
     * @return Money
     */
    public static function free_of_charge(Currency $currency)
    {
        return new self(0, $currency);
    }

    /**
     * @since 0.8
     * @param int|float|double|string $value
     * @param Currency $currency
     */
    public function __construct($value, Currency $currency)
    {
        if(is_string($value)) {
            $value = str_replace(',', '.', $value);
            $value = floatval($value);
        }

        if(is_numeric($value)) {
            $value = number_format($value, 2, '.', '');
        }

        Assert::stringNotEmpty($value, 'The money value must be a non empty string. Got: %s');

        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * Get the money value.
     *
     * @since 0.8
     * @return int
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Get the money currency.
     *
     * @since 0.8
     * @return Currency
     */
    public function get_currency()
    {
        return $this->currency;
    }

    /**
     * Check if this money is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_value() === $other->get_value() &&
            $this->get_currency()->is_equal_to($other->get_currency());
    }

    /**
     * Check of the given money is smaller than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.8
     * @param Money $money
     * @return bool
     */
    public function is_smaller_than(Money $money)
    {
        return
            $this->get_value() < $money->get_value() &&
            $this->get_currency()->is_equal_to($money->get_currency());
    }

    /**
     * Check of the given money is greater than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.8
     * @param Money $money
     * @return bool
     */
    public function is_greater_than(Money $money)
    {
        return
            $this->get_value() > $money->get_value() &&
            $this->get_currency()->is_equal_to($money->get_currency());
    }

    /**
     * Check of the given money is smaller than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.8
     * @param Money $money
     * @return bool
     */
    public function is_smaller_than_or_equal_to(Money $money)
    {
        return
            $this->is_smaller_than($money) ||
            $this->is_equal_to($money);
    }

    /**
     * Check of the given money is greater than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.8
     * @param Money $money
     * @return bool
     */
    public function is_greater_than_or_equal_to(Money $money)
    {
        return
            $this->is_smaller_than($money) ||
            $this->is_equal_to($money);
    }

    /**
     * Change the currency of the money.
     *
     * @since 0.8
     * @param Currency $currency
     * @return Money
     */
    public function change_currency(Currency $currency)
    {
        return new Money($this->value, $currency);
    }

    /**
     * Print the money.
     *
     * @since 0.8
     * @return null|string
     */
    public function __toString()
    {
        $currency_symbol = $this->currency->get_symbol();
        if (empty($value) || empty($currency_symbol)) {
            return null;
        }

        $value = number_format($value, 2, '.', '');
        $money = $value . ' ' . $currency_symbol;

        return $money;
    }
}
