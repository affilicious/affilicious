<?php
namespace Affilicious\Product\Model\Review;

use Affilicious\Common\Exception\Invalid_Type_Exception;
use Affilicious\Common\Model\Abstract_Value_Object;
use Affilicious\Product\Exception\Invalid_Big_Number_Exception;
use Affilicious\Product\Exception\Invalid_Small_Number_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Rating extends Abstract_Value_Object
{
    const MIN = 0;
    const MAX = 5;

    /**
     * Get a rating with the min value
     *
     * @since 0.6
     * @return Rating
     */
    public static function get_min()
    {
        return new self(self::MIN);
    }

    /**
     * Get a rating with the max value
     *
     * @since 0.6
     * @return Rating
     */
    public static function get_max()
    {
        return new self(self::MAX);
    }

    /**
     * @inheritdoc
     * @since 0.6
     * @throws Invalid_Type_Exception
     * @throws Invalid_Small_Number_Exception
     * @throws Invalid_Big_Number_Exception
     */
    public function __construct($value)
    {
        if (is_numeric($value)) {
            $value = floatval($value);
        }

        if (!is_float($value)) {
            throw new Invalid_Type_Exception($value, 'float');
        }

        if($value < self::MIN) {
            throw new Invalid_Small_Number_Exception($value, self::MIN);
        }

        if($value > self::MAX) {
            throw new Invalid_Big_Number_Exception($value, self::MAX);
        }

        parent::__construct($value);
    }
}
