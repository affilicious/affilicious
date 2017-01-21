<?php
namespace Affilicious\Shop\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Availability
{
    const AVAILABLE = 'available';
    const OUT_OF_STOCK = 'out-of-stock';

    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * Create the availability for available.
     *
     * @since 0.8
     * @return Availability
     */
    public static function available()
    {
        return new self(self::AVAILABLE);
    }

    /**
     * Create the availability for out of stock.
     *
     * @since 0.8
     * @return Availability
     */
    public static function out_of_stock()
    {
        return new self(self::OUT_OF_STOCK);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct($value)
    {
        $values = apply_filters('affilicious_shop_availability_values', array(
            self::AVAILABLE,
            self::OUT_OF_STOCK
        ));

        Assert::stringNotEmpty($value, 'The availability must be a non empty string. Got: %s');
        Assert::oneOf($value, $values, 'Expected availability of: %2$s. Got: %s');

        $this->set_value($value);
    }

    /**
     * Check if the availability is available.
     *
     * @since 0.8
     * @return bool
     */
    public function is_available()
    {
        return $this->value === self::AVAILABLE;
    }

    /**
     * Check if the availability is out of stock.
     *
     * @since 0.8
     * @return bool
     */
    public function is_out_of_stock()
    {
        return $this->value === self::OUT_OF_STOCK;
    }
}
