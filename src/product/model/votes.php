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
class Votes
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
     * Get the min votes.
     *
     * @since 0.8
     * @return Votes
     */
    public static function min()
    {
        return new self(self::MIN);
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

        Assert_Helper::is_integer($value, __METHOD__, 'Expected votes to be an integer. Got: %s', '0.9.2');
        Assert_Helper::greater_than_or_equal($value, self::MIN, __METHOD__, 'Expected votes to be greater than %s. Got: %s', '0.9.2');

        $this->set_value($value);
    }
}
