<?php
namespace Affilicious\Common\Model;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Simple_Value_Trait
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @since 0.8
     * @param mixed $value
     */
    public function __construct($value)
    {
        Assert::notNull($value);

        $this->value = $value;
    }

    /**
     * Get the value.
     *
     * @since 0.8
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Check if the simple value is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_value() == $other->get_value();
    }

    /**
     * Get the string representation of the value.
     *
     * @since 0.8
     * @return string
     */
    public function __toString()
    {
        return strval($this->value);
    }
}
