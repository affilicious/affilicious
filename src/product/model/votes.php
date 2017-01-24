<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Simple_Value_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Votes
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

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

        Assert::integer($value, 'Expected votes to be an integer. Got: %s');
        Assert::greaterThan($value, self::MIN, 'Expected votes greater than %2$s. Got: %s');

        $this->set_value($value);
    }
}
