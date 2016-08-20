<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Helper;

class DetailGroupHelper
{
    /**
     * Convert the detail name to the key which can be stored into the database
     *
     * @since 0.3
     * @param string $name
     * @return string
     */
    public static function convertNameToKey($name)
    {
        $key = str_replace(' ', '_', $name);
        $key = sanitize_title($key);

        return $key;
    }
}
