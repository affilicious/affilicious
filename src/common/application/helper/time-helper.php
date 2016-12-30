<?php
namespace Affilicious\Common\Application\Helper;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Time_Helper
{
    /**
     * Convert the timestamp into a datetime string.
     *
     * @since 0.7.1
     * @param $timestamp
     * @return string
     */
    public static function get_datetime_i18n($timestamp)
    {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $datetime = date_i18n($date_format . ' ' . $time_format, $timestamp);

        return $datetime;
    }
}
