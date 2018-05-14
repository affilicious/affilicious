<?php
namespace Affilicious\Common\Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.18
 */
class Value_Helper
{
    /**
     * @since 0.9.18
     * @param array $value_objects
     * @return int[]|bool[]|string[]|float[]
     */
	public static function to_scalars(array $value_objects)
	{
		$scalars = array_map(function($value_object) {
		    return method_exists($value_object, 'get_value') ? $value_object->get_value() : null;
        }, $value_objects);

		return $scalars;
	}

    /**
     * @since 0.9.18
     * @param array $scalars
     * @param $value_object_class
     * @return array
     */
	public static function from_scalars(array $scalars, $value_object_class)
    {
        $value_objects = array_map(function($scalar) use ($value_object_class) {
            return new $value_object_class($scalar);
        }, $scalars);

        return $value_objects;
    }

    /**
     * @since 0.9.18
     * @param $glue
     * @param array $value_objects
     * @return string
     */
	public static function implode($glue, array $value_objects)
    {
        $scalars = self::to_scalars($value_objects);

        $string = implode($glue, $scalars);

        return $string;
    }

    /**
     * @since 0.9.18
     * @param $delimiter
     * @param $string
     * @param $value_object_class
     * @param null $limit
     * @return array
     */
    public static function explode($delimiter, $string, $value_object_class, $limit = null)
    {
        $scalars = explode($delimiter, $string, $limit);

        $value_objects = self::from_scalars($scalars, $value_object_class);

        return $value_objects;
    }
}
