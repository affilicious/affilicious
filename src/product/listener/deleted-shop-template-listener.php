<?php
namespace Affilicious\Product\Listener;

use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Deleted_Shop_Template_Listener
{
	/**
	 * An array of changed term IDs and slugs
	 *
	 * @var array
	 */
	protected $changed_terms = [];

	/**
	 * @var Key_Generator_Interface
	 */
	protected $key_generator;

	/**
	 * @since 0.9.2
	 * @param Key_Generator_Interface $key_generator
	 */
	public function __construct(Key_Generator_Interface $key_generator)
	{
		$this->key_generator = $key_generator;
	}

	/**
	 * @hook delete_aff_shop_tmpl
	 * @since 0.9.2
	 * @param int $term_id Term ID.
	 * @param int $taxonomy_id Term taxonomy ID.
	 * @param \WP_Term|\WP_Error $deleted_term Copy of the already-deleted term, in the form specified by the parent function. WP_Error otherwise.
	 */
	public function delete($term_id, $taxonomy_id, $deleted_term)
	{
		global $wpdb;

		if(!($deleted_term instanceof \WP_Term)) {
			return;
		}

		$key = $this->key_generator->generate_from_slug(new Slug($deleted_term->slug));
		$key = $key->get_value();

		$wpdb->query("
			DELETE
			FROM $wpdb->postmeta
			WHERE meta_key LIKE '_affilicious_product_shops_$key-%';
		");
	}
}
