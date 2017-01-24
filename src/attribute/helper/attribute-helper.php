<?php
namespace Affilicious\Attribute\Helper;

use Affilicious\Attribute\Model\Attribute;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute_Helper
{
    /**
     * Convert the attribute into an array.
     *
     * @since 0.8
     * @param Attribute $attribute
     * @return array
     */
    public static function to_array(Attribute $attribute)
    {
        $raw_attribute = array(
            'template_id' => $attribute->get_template_id(),
            'name' => $attribute->get_name()->get_value(),
            'slug' => $attribute->get_slug()->get_value(),
            'type' => $attribute->get_type()->get_value(),
            'unit' => $attribute->has_unit() ? $attribute->get_unit()->get_value() : null,
            'value' => $attribute->get_value()->get_value(),
        );

        return $raw_attribute;
    }
}
