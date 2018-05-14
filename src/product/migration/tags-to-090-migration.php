<?php
namespace Affilicious\Product\Migration;

use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
final class Tags_To_090_Migration
{
	/**
	 * @since 0.9
	 * @var string
	 */
	const OPTION = 'aff_migrated_tags_to_0.9.0';

	/**
	 * @since 0.9
	 * @var Detail_Template_Repository_Interface
	 */
	protected $detail_template_repository;

	/**
	 * @since 0.9
	 * @var Attribute_Template_Repository_Interface
	 */
	protected $attribute_template_repository;

	/**
	 * @since 0.9
	 * @param Detail_Template_Repository_Interface $detail_template_repository
	 * @param Attribute_Template_Repository_Interface $attribute_template_repository
	 */
	public function __construct(
		Detail_Template_Repository_Interface $detail_template_repository,
		Attribute_Template_Repository_Interface $attribute_template_repository
	) {
		$this->detail_template_repository = $detail_template_repository;
		$this->attribute_template_repository = $attribute_template_repository;
	}

	/**
	 * Refresh the slugs to migrate the new hook priorities.
	 *
	 * @since 0.9
	 */
	public function migrate()
	{
		if(\Affilicious::VERSION >= '0.9' && get_option(self::OPTION) != 'yes') {
			$this->migrate_product_tags();
			$this->migrate_enabled_details_tags();
			$this->migrate_enabled_attributes_tags();

			update_option(self::OPTION, 'yes');
		}
	}

	/**
	 * Migrate the product tags
	 *
	 * @since 0.9
	 */
	protected function migrate_product_tags()
	{
		global $wpdb;

		$results = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = '_affilicious_product_tags'
            OR meta_key LIKE '_affilicious_product_variants_-_tags_%'
        ", ARRAY_A);

		foreach ($results as $result) {
			if(empty($result['meta_value']) || strpos($result['meta_value'], ',') !== false) {
				continue;
			}

			$tags = explode(';', $result['meta_value']);

			if(!empty($tags) && is_array($tags)) {
				$tags = array_unique($tags);
			}

			$tags = implode(',', $tags);

			update_post_meta($result['post_id'], $result['meta_key'], $tags);
		}
	}

	/**
	 * Migrate the enabled details tags.
	 *
	 * @since 0.9
	 */
	protected function migrate_enabled_details_tags()
	{
		global $wpdb;

		$results = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = '_affilicious_product_enabled_details'
        ", ARRAY_A);

		foreach ($results as $result) {
			if(empty($result['meta_value'])) {
				continue;
			}

			if(strpos($result['meta_value'], ';') >= 0) {
				$names = explode(';', $result['meta_value']);
			} else {
				$names = explode(',', $result['meta_value']);
			}

			if(empty($names)) {
				continue;
			}

			$ids = [];
			foreach ($names as $name) {
				if(is_numeric($name)) {
					$ids[] = $name;
					continue;
				}

				$detail_template = $this->detail_template_repository->find_one_by_name(new Name($name));
				if($detail_template === null) {
					continue;
				}

				$ids[] = intval($detail_template->get_id()->get_value());
			}

			$unique_ids = [];
			foreach($ids as $id) {
				if(!in_array($id, $unique_ids)) {
					$unique_ids[] = $id;
				}
			}

			$unique_ids = implode(',', $unique_ids);

			update_post_meta($result['post_id'], $result['meta_key'], $unique_ids);
		}
	}

	/**
	 * Migrate the enabled attributes tags.
	 *
	 * @since 0.9
	 */
	protected function migrate_enabled_attributes_tags()
	{
		global $wpdb;

		$results = $wpdb->get_results("
            SELECT *
            FROM $wpdb->postmeta
            WHERE meta_key = '_affilicious_product_enabled_attributes'
            OR meta_key LIKE '_affilicious_product_variants_-_enabled_attributes_%'
        ", ARRAY_A);

		foreach ($results as $result) {
			if(empty($result['meta_value'])) {
				continue;
			}

			if(strpos($result['meta_value'], ';') >= 0) {
				$names = explode(';', $result['meta_value']);
			} else {
				$names = explode(',', $result['meta_value']);
			}

			if(empty($names)) {
				continue;
			}

			$ids = [];
			foreach ($names as $name) {
				if(is_numeric($name)) {
					$ids[] = $name;
					continue;
				}

				$attributes_template = $this->attribute_template_repository->find_one_by_name(new Name($name));
				if($attributes_template === null) {
					continue;
				}

				$ids[] = intval($attributes_template->get_id()->get_value());
			}

			$unique_ids = [];
			foreach($ids as $id) {
				if(!in_array($id, $unique_ids)) {
					$unique_ids[] = $id;
				}
			}

			$unique_ids = implode(',', $unique_ids);

			update_post_meta($result['post_id'], $result['meta_key'], $unique_ids);
		}
	}
}
