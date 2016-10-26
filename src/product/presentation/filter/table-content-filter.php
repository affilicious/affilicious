<?php
namespace Affilicious\Product\Presentation\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Table_Content_Filter
{
    /**
     * Hide the product variants from the product table list
     *
     * @since 0.6
     * @param \WP_Query $query
     */
    public function filter(\WP_Query $query)
    {
        if(is_admin() && !empty($_GET['post_type']) && $_GET['post_type'] == 'product' && $query->query['post_type'] == 'product' && !current_user_can( 'be_overlord' )) {
            $query->query_vars['post_parent'] = 0;
        }
    }
}
