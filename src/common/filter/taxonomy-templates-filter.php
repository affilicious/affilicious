<?php
namespace Affilicious\Common\Filter;

use Affilicious\Product\Model\Product;

if(!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.8
 */
class Taxonomy_Templates_Filter
{
	/**
	 * @since 0.9.8
	 * @var string
	 */
	const PRODUCT_ARCHIVE_TEMPLATE = 'archive-aff_product.php';

	/**
	 * Add the product archive to the taxonomies.
	 *
	 * @filter taxonomy_template_hierarchy
	 * @since 0.9.8
	 * @param array $templates
	 * @return array
	 */
	public function filter($templates = [])
	{
		$has_product_archive = in_array(self::PRODUCT_ARCHIVE_TEMPLATE, $templates);
		if($has_product_archive) {
			return $templates;
		}

		$term = get_queried_object();
		if(!($term instanceof \WP_Term)) {
			return $templates;
		}

		$taxonomy = get_taxonomy($term->taxonomy);
		if(!($taxonomy instanceof \WP_Taxonomy)) {
			return $templates;
		}

		$is_product = in_array(Product::POST_TYPE, $taxonomy->object_type);
		if(!$is_product) {
			return $templates;
		}

		array_push($templates, self::PRODUCT_ARCHIVE_TEMPLATE);

		return $templates;
	}
}
