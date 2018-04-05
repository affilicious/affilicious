<?php
namespace Affilicious\Product\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.24
 */
class Disable_Complex_Products_For_Query_Filter
{
    /**
     * Filter the complex products in the front end. Just show the variants.
     *
     * @filter pre_get_posts
     * @since 0.9.24
     * @param \WP_Query $query
     */
    public function filter(\WP_Query $query)
    {
        if (!is_admin() && $this->are_disabled_for_query($query)) {
            $query->set('meta_query', [
                'relation'    => 'OR',
                [
                    'key'          => '_affilicious_product_type',
                    'value'        => 'complex',
                    'compare'      => '!=',
                ],
                [
                    'key' => '_affilicious_product_type',
                    'compare' => 'NOT EXISTS'
                ],
            ]);
        }
    }

	/**
	 * @since 0.9.24
	 * @see https://core.trac.wordpress.org/ticket/21790
	 * @param \WP_Query $query
	 * @return bool
	 */
    protected function are_disabled_for_query(\WP_Query $query)
    {
    	$disabled =
	        $query->is_main_query() &&
	        (
	        	$query->is_archive() ||
		        $query->is_search() ||
		        $query->is_home() ||
		        $query->get('page_id') == get_option('page_on_front') ||
		        $query->is_front_page()
	        )
	    ;

	    /**
	     * Filter whether the complex products are disabled for the query or not.
	     *
	     * @since 0.9.24
	     * @var bool $disabled Whether the complex products are disabled or not.
	     * @var \WP_Query $query The query for the posts.
	     */
    	$disabled = apply_filters('aff_complex_products_disabled_for_query', $disabled, $query);

    	return $disabled;
    }
}
