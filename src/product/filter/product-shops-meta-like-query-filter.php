<?php
namespace Affilicious\Product\Filter;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.15
 */
class Product_Shops_Meta_Like_Query_Filter
{
	/**
	 * @since 0.9.15
	 * @param string $where
	 * @param $query
	 * @return string
	 */
	public function filter($where, $query)
	{
		global $wpdb;

		if ($query->query_vars['post_type'] == Product::POST_TYPE) {
			if(method_exists($wpdb, 'remove_placeholder_escape')) {
				$where = $wpdb->remove_placeholder_escape($where);
			}

			$where = str_replace("meta_key = '_affilicious_product_shops_%-_affiliate_product_id_%", "meta_key LIKE '_affilicious_product_shops_%-_affiliate_product_id_%", $where );

			if(method_exists($wpdb, 'add_placeholder_escape')) {
				$where = $wpdb->add_placeholder_escape($where);
			}
		}

		return $where;
	}
}
