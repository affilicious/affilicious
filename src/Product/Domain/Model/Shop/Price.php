<?php
namespace Affilicious\Product\Domain\Model\Shop;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Price extends AbstractAggregate
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @since 0.5.2
     * @param int|float|double $value
     * @param Currency $currency
     */
    public function __construct($value, Currency $currency)
    {
        if(!is_numeric($value)) {
            throw new InvalidTypeException($value, 'int|float|double');
        }

        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * Get the price value
     *
     * @since 0.5.2
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the price currency
     *
     * @since 0.5.2
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     * @since 0.5.2
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getValue() === $object->getValue() &&
            $this->getCurrency()->isEqualTo($object->getCurrency());
    }

    /**
     * Check of the given price is smaller than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.5.2
     * @param mixed|Price $price
     * @return bool
     */
    public function isSmallerThan($price)
    {
        return
            $price instanceof self &&
            $this->getValue() < $price->getValue() &&
            $this->getCurrency()->isEqualTo($price->getCurrency());
    }

    /**
     * Check of the given price is greater than the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.5.2
     * @param mixed|Price $price
     * @return bool
     */
    public function isGreaterThan($price)
    {
        return
            $price instanceof self &&
            $this->getValue() > $price->getValue() &&
            $this->getCurrency()->isEqualTo($price->getCurrency());
    }

    /**
     * Check of the given price is smaller than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.5.2
     * @param mixed|Price $price
     * @return bool
     */
    public function isSmallerThanOrEqualTo($price)
    {
        return
            $price instanceof self &&
            $this->isSmallerThan($price) ||
            $this->isEqualTo($price);
    }

    /**
     * Check of the given price is greater than or equal to the current one.
     * Please not that this method always return false on different currencies.
     *
     * @since 0.5.2
     * @param mixed|Price $price
     * @return bool
     */
    public function isGreaterThanOrEqualTo($price)
    {
        return
            $price instanceof self &&
            $this->isSmallerThan($price) ||
            $this->isEqualTo($price);
    }
}
