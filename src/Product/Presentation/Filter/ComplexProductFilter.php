<?php
namespace Affilicious\Product\Presentation\Filter;

use Affilicious\Product\Domain\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ComplexProductFilter
{
    function filter(\WP_Query $query)
    {
        if ($query->is_main_query() && ($query->is_archive() || $query->is_search() || $query->is_home())) {
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
