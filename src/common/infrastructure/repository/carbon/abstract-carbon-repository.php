<?php
namespace Affilicious\Common\Infrastructure\Repository\Carbon;

use Affilicious\Common\Infrastructure\Repository\Wordpress\Abstract_Wordpress_Repository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Carbon_Repository extends Abstract_Wordpress_Repository
{
    /**
     * Builds the complex carbon post meta keys for the storage
     * This method works recursively and it's not easy to understand.
     *
     * @see https://carbonfields.net/docs/complex-field-data-storage/
     * @since 0.6
     * @param array $values
     * @param string $prefix
     * @param int $depth
     * @param string|int $prev_key
     * @return array
     *
     * Example process:
     *  _affilicious_product_variants_-_shops_0_amazon-_title_0  -> complex
     *  _-_shops_0_amazon-_title_0                               -> group
     *  _shops_0_amazon-_title_0                                 -> field
     *  _amazon-_title_0                                         -> group
     *  _title_0                                                 -> field
     *
     * Example input:
     * array(
     *   '_' => array(
     *     0 => array(
     *       'title' => '_product _variant 1',
     *       'thumbnail' => 'http://url-to-thumbnail.com',
     *       'shops' => array(
     *         'amazon' =>array(
     *           0 => array(
     *             'shop_id' => 3,
     *             'title' => '_amazon',
     *           )
     *         )
     *       )
     *     )
     *   )
     * )
     *
     * Example output:
     * array(
     *    _affilicious_product_variants_-_title_0 => '_product _variant 1',
     *    _affilicious_product_variants_-_thumbnail_0 => 'http://url-to-thumbnail.com',
     *    _affilicious_product_variants_-_shops_0_amazon-_shop_id_0 => 3,
     *    _affilicious_product_variants_-_shops_0_amazon-_title_0 => '_amazon',
     * )
     */
    protected function build_complex_carbon_meta_key($values, $prefix, $depth = 0, $prev_key = '')
    {
        $regex_complex = '_%s';
        $regex_group = '_%s-';
        $regex_field = '_%s_%d';

        if($depth === 0) {
            $prefix = sprintf($regex_complex, $prefix);
        }

        $temp = array();
        if(is_array($values)) {
            foreach ($values as $key => $value) {

                // _key is a string. _entry might be a complex field, a group or a simple field
                if(is_string($key)) {

                    // _value is an array. _entry might be a complex field or a group
                    if(is_array($value)) {

                        // _the previous key is an int. _entry must be a complex field
                        if(is_int($prev_key)) {
                            $_prefix = $prefix . sprintf($regex_field, $key, $prev_key);
                            $temp[] = $this->build_complex_carbon_meta_key($value, $_prefix, $depth + 1, $key);

                            // _the previous key is a string. _entry must be a group
                        } else {
                            $_prefix = $prefix . sprintf($regex_group, $key);
                            $temp[] = $this->build_complex_carbon_meta_key($value, $_prefix, $depth + 1, $key);
                        }

                        // _key is a string. _entry must be a simple field
                    } else {
                        $_prefix = $prefix . sprintf($regex_field, $key, $prev_key);
                        $temp[$_prefix] = $value;
                    }

                    // _key is int. _entry must be a repeatable field
                } else {
                    $temp[] = $this->build_complex_carbon_meta_key($value, $prefix, $depth + 1, $key);
                }
            }
        }

        // _break the recursion
        if($depth > 0) {
            return $temp;
        }

        // _remove the nested arrays
        $result = array();
        array_walk_recursive($temp, function($value, $key) use (&$result) {
            $result[$key] = $value;
        });

        return $result;
    }
}
