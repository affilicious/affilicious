<?php
namespace Affilicious\Product\Admin\Setup;

use Affilicious\Common\Helper\Assert_Helper;
use Affilicious\Common\Helper\Template_Helper;
use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Import_Page_Setup
{
	const PAGE_SLUG = 'aff-import-%s';

	/**
	 * Set up all product import pages.
	 *
	 * @hook admin_menu
	 * @since 0.9.4
	 */
	public function init()
	{
		// Find all import pages
		$import_pages = $this->find_import_pages();
		if(empty($import_pages)) {
			return;
		}

		do_action('aff_product_admin_before_import_pages', $import_pages);

		// Add the pages to Wordpress.
		foreach($import_pages as $index => $import_page) {

			// Check if the import pages contain the required keys to call "add_submenu_page".
			$required_keys = ['title', 'slug', 'render'];
			foreach ($required_keys as $required_key) {
				Assert_Helper::key_exists($import_page, $required_key, __METHOD__, sprintf(__('The import page has to contain the key "%s" to call the Wordpress function "add_submenu_page".', 'affilicious-ebay'), $required_key), '0.9.4');
			}

			add_submenu_page(
				$index == 0 ? 'edit.php?post_type=aff_product' : null,
				$import_page['title'],
				__('Import', 'affilicious'),
				'manage_options',
				sprintf(self::PAGE_SLUG, $import_page['slug']),
				array($this, 'render')
			);
		}

		do_action('aff_product_admin_after_import_pages', $import_pages);
	}

	/**
	 * Render the product import page.
	 * This method is like a proxy, which wraps the import page into a wrapper with a tab navigation.
	 *
	 * @since 0.9.4
	 */
	public function render()
	{
		// Find all import pages
		$import_pages = $this->find_import_pages();
		if(empty($import_pages)) {
			return;
		}

		// Build the admin urls for the tab navigation
		$admin_urls = [];
		foreach ($import_pages as $import_page) {
			$admin_urls[$import_page['slug']] = admin_url($this->build_url($import_page));
		}

		// Render the import page
		foreach ($import_pages as $import_page) {
			if($this->is_current_page($import_page)) {
				Template_Helper::render('admin/page/imports', [
					'import_pages' => $import_pages,
					'current_import_page' => $import_page,
					'admin_urls' => $admin_urls
				]);
			}
		}
	}

	/**
	 * Keep the import submenu highlighted on all import pages.
	 *
	 * @filter parent_file
	 * @since 1.0
	 * @param string $file
	 * @return string
	 */
	public function highlighted_url($file)
	{
		global $submenu_file;

		// Find all import page.
		$import_pages = $this->find_import_pages();
		if(empty($import_pages)) {
			return $file;
		}

		// Find the current highlighted page.
		foreach ($import_pages as $import_page) {
			if($this->is_current_page($import_page)) {
				$submenu_file = $this->build_import_page_slug($import_pages[0]);
			}
		}

		return $file;
	}

	/**
	 * Find all product import pages.
	 *
	 * @since 1.0
	 * @return array
	 */
	private function find_import_pages()
	{
		// Collect all import pages
		$import_pages = apply_filters('aff_product_admin_import_pages', []);
		Assert_Helper::is_array($import_pages, __METHOD__, 'The import pages have to be arrays.', '0.9.4');
		if(empty($import_pages)) {
			return [];
		}

		// Sort the import pages
		ksort($import_pages);
		$import_pages = array_reverse($import_pages);
		$import_pages = array_values($import_pages);

		return $import_pages;
	}

	/**
	 * Build the url for the import page.
	 *
	 * @since 1.0
	 * @param array $import_page
	 * @return string
	 */
	private function build_url(array $import_page)
	{
		return 'edit.php?post_type=' . Product::POST_TYPE .'&page=' . sprintf(self::PAGE_SLUG, $import_page['slug']);
	}

	/**
	 * Check if the import page is the current screen.
	 *
	 * @since 1.0
	 * @param array $import_page
	 * @return bool
	 */
	private function is_current_page(array $import_page)
	{
		// Get the current import page to render it.
		$screen = get_current_screen();
		if(empty($screen)) {
			return false;
		}

		return $screen->id == 'aff_product_page_' . sprintf(self::PAGE_SLUG, $import_page['slug']);
	}

	/**
	 * Build the full import page slug.
	 *
	 * @since 1.0
	 * @param array $import_page
	 * @return string
	 */
	private function build_import_page_slug(array $import_page)
	{
		return sprintf(self::PAGE_SLUG, $import_page['slug']);
	}
}
