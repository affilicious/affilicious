<?php
namespace Affilicious\Common\Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7.1
 */
class Time_Helper
{
    /**
     * Convert the timestamp into a datetime string.
     *
     * @since 0.7.1
     * @param int|string|\DateTimeInterface $timestamp
     * @return string
     */
    public static function to_datetime_i18n($timestamp)
    {
        if($timestamp instanceof \DateTimeInterface) {
            $timestamp = $timestamp->getTimestamp();
        }

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $format = $date_format . ' ' . $time_format;

        $datetime = date_i18n($format, $timestamp);

        return $datetime;
    }

    /**
     * @since 0.9
     * @param string $datetime_i18n
     * @return null|\DateTimeImmutable
     */
    public static function to_datetime_immutable_object($datetime_i18n)
    {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $format = $date_format . ' ' . $time_format;

        $datetime = \DateTimeImmutable::createFromFormat($format, $datetime_i18n);
        if($datetime === false) {
            return null;
        }

        return $datetime;
    }
}
