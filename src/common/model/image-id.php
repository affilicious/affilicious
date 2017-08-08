<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.1
 * @package Affilicious\Common\Model
 */
class Image_Id
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct($value)
    {
        if (is_numeric($value) || is_string($value)) {
            $value = intval($value);
        }

        Assert_Helper::is_integer($value, __METHOD__, 'Expected the image ID to be an integer. Got: %s', '0.9.2');

        $this->set_value($value);
    }

    /**
     * Just here to make the migration to "Image" easier.
     *
     * @deprecated 1.1
     * @since 0.9
     * @return int
     */
    public function get_id()
    {
        return $this->value;
    }

    /**
     * Just here to make the migration to "Image" easier.
     *
     * @deprecated 1.1
     * @since 0.9
     * @return null|string
     */
    public function get_src()
    {
        return !empty($this->value) ? get_the_post_thumbnail_url($this->value, 'full') : null;
    }
}
