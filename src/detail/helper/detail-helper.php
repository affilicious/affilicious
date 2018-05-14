<?php
namespace Affilicious\Detail\Helper;

use Affilicious\Detail\Model\Detail;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8.9
 */
class Detail_Helper
{
    /**
     * Convert the detail into an array.
     *
     * @since 0.8.9
     * @param Detail $detail
     * @return array
     */
    public static function to_array(Detail $detail)
    {
        $array = array(
            'template_id' => $detail->get_template_id()->get_value(),
            'name' => $detail->get_name()->get_value(),
            'slug' => $detail->get_slug()->get_value(),
            'type' => $detail->get_type()->get_value(),
            'unit' => $detail->has_unit() ? $detail->get_unit()->get_value() : null,
            'value' => $detail->get_value()->get_value(),
	        'custom_values' => $detail->has_custom_values() ? $detail->get_custom_values() : null,
        );

        $array = apply_filters('aff_detail_to_array', $array, $detail);

        return $array;
    }
}
