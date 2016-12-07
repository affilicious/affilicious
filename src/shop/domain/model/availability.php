<?php
namespace Affilicious\Shop\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Availability extends Abstract_Value_Object
{
    const AVAILABLE = 'available';
    const OUT_OF_STOCK = 'out-of-stock';

    /**
     * Create the availability for available.
     *
     * @since 0.7
     * @return Availability
     */
    public static function available()
    {
        return new self(self::AVAILABLE);
    }

    /**
     * Create the availability for out of stock.
     *
     * @since 0.7
     * @return Availability
     */
    public static function out_of_stock()
    {
        return new self(self::OUT_OF_STOCK);
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws Invalid_Type_Exception
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        $codes = array(
            self::AVAILABLE,
            self::OUT_OF_STOCK
        );

        if (!is_string($value)) {
            throw new Invalid_Type_Exception($value, 'string');
        }

        if(!in_array($value, $codes)) {
            throw new \InvalidArgumentException(sprintf(
                'The availability "%" is not valid. Please choose from "%s"',
                $value,
                implode(', ', $codes)
            ));
        }

        parent::__construct($value);
    }

    /**
     * Check if the availability is available.
     *
     * @since 0.7
     * @return bool
     */
    public function is_available()
    {
        return $this->value === self::AVAILABLE;
    }

    /**
     * Check if the availability is out of stock.
     *
     * @since 0.7
     * @return bool
     */
    public function is_out_of_stock()
    {
        return $this->value === self::OUT_OF_STOCK;
    }
}
