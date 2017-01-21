<?php
namespace Affilicious\Product\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Complex_Product_Filter
{
    /**
     * Filter the complex products in the front end. Just show the variants.
     *
     * @since 0.6
     * @param \WP_Query $query
     */
    function filter(\WP_Query $query)
    {
        if (!is_admin() && $query->is_main_query() && ($query->is_archive() || $query->is_search() || $query->is_home())) {
            $query->set('meta_query', array(
                'relation'    => 'OR',
                array(
                    'key'          => '_affilicious_product_type',
                    'value'        => 'complex',
                    'compare'      => '!=',
                ),
                array(
                    'key' => '_affilicious_product_type',
                    'compare' => 'NOT EXISTS'
                ),
            ));
        }
    }
}
