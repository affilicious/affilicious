<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Rating
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    const MIN = 0;
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
            $value = intval($value);
        }

        Assert::integer($value, 'Expected rating to be an integer. Got: %s');
        Assert::range($value, self::MIN, self::MAX, 'Expected rating between %2$s and %3$s. Got: %s');

        $this->set_value($value);
    }
}
