<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;
use Affilicious\Product\Domain\Exception\Invalid_Small_Number_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Votes extends Abstract_Value_Object
{
    const MIN = 0;

    /**
     * Get a votes with the min value
     *
     * @since 0.6
     * @return Votes
     */
    public static function get_min()
    {
        return new self(self::MIN);
    }

    /**
     * @since 0.6
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (is_numeric($value)) {
            $value = intval($value);
        }

        if (!is_int($value)) {
            throw new Invalid_Type_Exception($value, 'int');
        }

        if($value < self::MIN) {
            throw new Invalid_Small_Number_Exception($value, self::MIN);
        }

        parent::__construct($value);
    }
}
