<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Model\Simple_Value_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Rating
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @since 0.8
	 * @var int
	 */
    const MIN = 0;

	/**
	 * @since 0.8
	 * @var int
	 */
    const MAX = 5;

    /**
     * Get the min rating.
     *
     * @since 0.8
     * @return Rating
     */
    public static function min()
    {
        return new self(self::MIN);
    }

    /**
     * Get the max rating.
     *
     * @since 0.8
     * @return Rating
     */
    public static function max()
    {
        return new self(self::MAX);
    }

    /**
     * @since 0.8
     * @param int $value
     */
    public function __construct($value)
    {
        if (is_numeric($value) || is_string($value)) {
            $value = floatval($value);
        }

        Assert_Helper::is_float($value, __METHOD__, 'Expected rating to be a float. Got: %s', '0.9.2');
        Assert_Helper::range($value, self::MIN, self::MAX, __METHOD__, 'Expected rating between %2$s and %3$s. Got: %s', '0.9.2');

        $this->set_value($value);
    }
}
