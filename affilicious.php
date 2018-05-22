<?php
/**
 * Plugin Name: Affilicious
 * Description: The simplest and most flexible Wordpress affiliate solution with products & variants, shops, price comparisons and much more.
 * Version: 0.10.2
 * Author: Affilicious
 * Plugin URI: https://affilicious.com/downloads/affilicious/
 * Author URI: https://affilicious.com/
 * License: GPL-2.0 or later
 * Requires at least: 4.5
 * Tested up to: 4.9.6
 * Text Domain: affilicious
 * Domain Path: languages/
 *
 * Affilicious
 * Copyright (C) 2016-2018, Affilicious - support@affilicious.com
 *
 * Affilicious is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Affilicious is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Affilicious. If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_BASE_NAME', plugin_basename(__FILE__));

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_ROOT_PATH', plugin_dir_path(__FILE__));

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_ROOT_URL', plugin_dir_url(__FILE__));

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_VERSION', '0.10.3');

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_MIN_PHP_VERSION', '5.6');

/**
 * @since 0.8
 * @var string
 */
define('AFFILICIOUS_MIN_WORDPRESS_VERSION', '4.5');

if(!class_exists('Affilicious')) {

	/**
	 * @since 0.3
	 */
	class Affilicious
	{
		/**
		 * @since 0.8
		 * @var string
		 */
		const NAME = 'affilicious';

		/**
		 * @since 0.8
		 * @var string
		 */
		const VERSION = AFFILICIOUS_VERSION;

		/**
		 * @since 0.8
		 * @var string
		 */
		const MIN_PHP_VERSION = AFFILICIOUS_MIN_PHP_VERSION;

		/**
		 * @since 0.8
		 * @var string
		 */
		const MIN_WORDPRESS_VERSION = AFFILICIOUS_MIN_WORDPRESS_VERSION;

		/**
		 * Stores the singleton instance
		 *
		 * @since 0.3
		 * @var Affilicious
		 */
		private static $instance;

		/**
		 * Register all services and parameters for the pimple dependency injection
		 *
		 * @see http://pimple.sensiolabs.org
		 * @var \Pimple\Container
		 */
		private $container;

		/**
		 * Get the instance of the affilicious plugin
		 *
		 * @since 0.3
		 * @return Affilicious
		 */
		public static function get_instance()
		{
			if (self::$instance === null) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Convenient way to get the service from the dependency injection container.
		 *
		 * @since 0.8
		 * @param string $service_id
		 * @return mixed|null
		 */
		public static function get($service_id)
		{
			$container = self::get_instance()->get_container();

			try {
				$service = $container[$service_id];
			} catch (Pimple\Exception\UnknownIdentifierException $e) {
				return null;
			}

			return $service;
		}

		/**
		 * Get the base name of the plugin.
		 *
		 * @since 0.9.23
		 * @return string
		 */
		public static function get_base_name()
		{
			return AFFILICIOUS_BASE_NAME;
		}

		/**
		 * Get the root url to the plugin.
		 *
		 * @since 0.7
		 * @return string
		 */
		public static function get_root_url()
		{
			return AFFILICIOUS_ROOT_URL;
		}

		/**
		 * Get the root path to the plugin.
		 *
		 * @since 0.7
		 * @return string
		 */
		public static function get_root_path()
		{
			return AFFILICIOUS_ROOT_PATH;
		}

		/**
		 * Prepare the plugin with for usage with Wordpress and namespaces.
		 *
		 * @since 0.3
		 */
		private function __construct()
		{
			// Check the PHP version and extensions requirement.
			if (!$this->check_requirements()) {
				return;
			}

			if (file_exists(__DIR__ . '/vendor/autoload.php')) {
				require(__DIR__ . '/vendor/autoload.php');
			}

			spl_autoload_register(array($this, 'autoload'));

			$this->container = new Pimple\Container();
		}

		/**
		 * Get a reference to the dependency injection container.
		 *
		 * @see http://pimple.sensiolabs.org
		 * @since 0.3
		 * @return Pimple\Container
		 */
		public function get_container()
		{
			return $this->container;
		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since 0.3
		 */
		public function run()
		{
			// Hook the plugin activation and deactivation.
			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'deactivate'));

			// Check the PHP version and extensions requirement.
			if (!$this->check_requirements()) {
				return;
			}

			// Load the functions, includes, services, hooks and etc.
			$this->load_functions();
			$this->load_services();
			$this->load_legacy();
			$this->migrate();
			$this->register_public_hooks();
			$this->register_admin_hooks();
		}

		/**
		 * Make namespaces compatible with the source code of this plugin.
		 *
		 * @since 0.3
		 * @param string $class
		 */
		public function autoload($class)
		{
			$prefix = 'Affilicious\\';
			$test_prefix = 'Affilicious_Tests\\';
			if (stripos($class, $prefix) === false && stripos($class, $test_prefix) === false) {
				return;
			}

			$file_path = str_ireplace($prefix, '', $class);
			$file_path = str_ireplace($test_prefix, '', $file_path);
			$file_path .= '.php';
			$file_path = strtolower($file_path);
			$file_path = str_replace('_', '-', $file_path);

			if (stripos($class, $test_prefix) !== false) {
				$file_path = __DIR__ . '/tests/' . $file_path;
			} else {
				$file_path = __DIR__ . '/src/' . $file_path;
			}

			$file_path = str_replace('\\', DIRECTORY_SEPARATOR, $file_path);

			/** @noinspection PhpIncludeInspection */
			include_once($file_path);
		}

		/**
		 * Check the plugin requirements.
		 *
		 * @since 0.9.12
		 * @return bool
		 */
		public function check_requirements()
		{
			return version_compare(phpversion(), self::MIN_PHP_VERSION, '>=') && extension_loaded('mbstring');
		}

		/**
		 * The code that runs during plugin activation.
		 *
		 * @since 0.3
		 * @param bool $network_wide Optional. Whether this plugin is activated network wide or not. Default: false.
		 */
		public function activate($network_wide = false)
		{
			global $wp_version;

			$this->load_textdomain();

			// Check the PHP version requirement
			if (!version_compare(phpversion(), self::MIN_PHP_VERSION, '>=')) {
				$error = sprintf(
					__('Affilicious requires at least the PHP Version %s to reveal the full potential. Please switch the PHP version in your hosting provider.', 'affilicious'),
					self::MIN_PHP_VERSION
				);
			}

			// Check the Wordpress version requirement.
			elseif(!version_compare($wp_version, self::MIN_WORDPRESS_VERSION, '>=')) {
				$error = sprintf(
					__('Affilicious requires at least the Wordpress Version %s. Please update Wordpress in the <a href="%s">updates area</a>.', 'affilicious'),
					self::MIN_WORDPRESS_VERSION,
					admin_url('update-core.php')
				);
			}

			// Check the mbstring extension requirement.
			elseif(!extension_loaded('mbstring')) {
				$error = sprintf(
					__('The Affilicious Plugin requires the PHP extension %s. Please install the PHP extension in your hosting provider.', 'affilicious'),
					'mbstring'
				);
			}

			// Trigger the error message.
			if(isset($error)) {
				deactivate_plugins(self::get_base_name());

				wp_die($error . '<br><br>' . sprintf(
					__('Go back to the <a href="%s">plugins area</a>', 'affilicious'),
					admin_url('plugins.php')
				));
			}

			// Activate the product slug rewrites.
			$product_slug_rewrite_setup = $this->container['affilicious.product.setup.slug_rewrite'];
			$product_slug_rewrite_setup->activate($network_wide);

			// Activate the automatic license checks.
			$license_timer = $this->container['affilicious.common.admin.license.timer'];
			$license_timer->activate($network_wide);

			// Activate the product updates.
			$product_update_timer = $this->container['affilicious.product.update.timer'];
			$product_update_timer->activate($network_wide);

			// Activate the logs cleaner.
			$logs_cleaner_timer = $this->container['affilicious.common.cleaner.logs_timer'];
			$logs_cleaner_timer->activate($network_wide);

			// Activate the orphaned product variants cleaner.
			$orphaned_product_variants_cleaner_timer = $this->container['affilicious.product.cleaner.orphaned_product_variants_timer'];
			$orphaned_product_variants_cleaner_timer->activate($network_wide);

			// Install the update semaphore.
			$product_update_semaphore = $this->container['affilicious.product.update.semaphore'];
			$product_update_semaphore->install($network_wide);

			// Install the logs table
            $logs_table_setup = $this->container['affilicious.common.setup.logs_table'];
            $logs_table_setup->init($network_wide);

			// Reset the download recommendation
            $download_recommendation_setup = $this->container['affilicious.common.admin.setup.download_recommendation'];
            $download_recommendation_setup->init($network_wide);
		}

		/**
		 * The code that runs during plugin deactivation.
		 *
		 * @since 0.3
		 * @param bool $network_wide Optional. Whether this plugin is deactivated network wide or not. Default: false.
		 */
		public function deactivate($network_wide = false)
		{
			// Deactivate the product slug rewrites.
			$product_slug_rewrite_setup = $this->container['affilicious.product.setup.slug_rewrite'];
			$product_slug_rewrite_setup->deactivate($network_wide);

			// Deactivate the automatic license checks.
			$license_timer = $this->container['affilicious.common.admin.license.timer'];
			$license_timer->deactivate($network_wide);

			// Deactivate the product updates.
			$product_update_timer = $this->container['affilicious.product.update.timer'];
			$product_update_timer->deactivate($network_wide);

			// Deactivate the logs cleaner.
			$logs_cleaner_timer = $this->container['affilicious.common.cleaner.logs_timer'];
			$logs_cleaner_timer->deactivate($network_wide);

			// Deactivate the orphaned product variants cleaner.
			$orphaned_product_variants_cleaner_timer = $this->container['affilicious.product.cleaner.orphaned_product_variants_timer'];
			$orphaned_product_variants_cleaner_timer->deactivate($network_wide);

			// Uninstall the update semaphore.
			$product_update_semaphore = $this->container['affilicious.product.update.semaphore'];
			$product_update_semaphore->uninstall($network_wide);
		}

		/**
		 * Migrate the old code to the new version.
		 *
		 * @since 0.8
		 */
		public function migrate()
		{
			add_action('admin_init', function() {
				$migrated = get_option('_affilicious_migrated_to_beta');
				if($migrated !== 'yes') {
					$product_post_type_migration = $this->container['affilicious.product.migration.post_type'];
					$product_post_type_migration->migrate();

					$shop_post_type_migration = $this->container['affilicious.shop.migration.post_type'];
					$shop_post_type_migration->migrate();

					$currency_code_migration = $this->container['affilicious.shop.migration.currency_code'];
					$currency_code_migration->migrate();

					$shop_post_to_term_migration = $this->container['affilicious.shop.migration.post_to_term'];
					$shop_post_to_term_migration->migrate();

					$detail_post_to_term_migration = $this->container['affilicious.detail.migration.post_to_term'];
					$detail_post_to_term_migration->migrate();

					$attribute_post_to_term_migration = $this->container['affilicious.attribute.migration.post_to_term'];
					$attribute_post_to_term_migration->migrate();

					$product_details_migration = $this->container['affilicious.product.migration.details'];
					$product_details_migration->migrate();

					$product_shops_migration = $this->container['affilicious.product.migration.shops'];
					$product_shops_migration->migrate();

					$product_variants_migration = $this->container['affilicious.product.migration.variants'];
					$product_variants_migration->migrate();

					add_option('_affilicious_migrated_to_beta', 'yes');
				}

				$cleaned_variants = get_option('_affilicious_migrated_to_beta_with_cleaned_variants');
				if($cleaned_variants !== 'yes') {
					$clean_variants_migration = $this->container['affilicious.product.migration.clean_variants'];
					$clean_variants_migration->migrate();

					add_option('_affilicious_migrated_to_beta_with_cleaned_variants', 'yes');
				}

				$inherit_status = get_option('_affilicious_migrated_to_beta_with_variant_status_inherit');
				if($inherit_status !== 'yes') {
					$variant_inherit_status_migration = $this->container['affilicious.product.migration.variant_inherit_status'];
					$variant_inherit_status_migration->migrate();

					add_option('_affilicious_migrated_to_beta_with_variant_status_inherit', 'yes');
				}

				$product_slug_migration = $this->container['affilicious.product.migration.product_slug'];
				$product_slug_migration->migrate();

				$affiliate_product_id_to_090_migration = $this->container['affilicious.product.migration.affiliate_product_id_to_090'];
				$affiliate_product_id_to_090_migration->migrate();

				$tags_to_090_migration = $this->container['affilicious.product.migration.tags_to_090'];
				$tags_to_090_migration->migrate();

				$product_variant_terms_to_0820_migration = $this->container['affilicious.product.migration.product_variant_terms_to_0820'];
				$product_variant_terms_to_0820_migration->migrate();

				$product_slugs_to_0818_migration = $this->container['affilicious.product.migration.product_slugs_to_0818'];
				$product_slugs_to_0818_migration->migrate();

				$product_updates_to_0920_migration = $this->container['affilicious.product.migration.product_updates_to_0920'];
				$product_updates_to_0920_migration->migrate();

				$non_existing_logs_table_to_0920_migration = $this->container['affilicious.common.migration.non_existing_logs_table_to_0920'];
				$non_existing_logs_table_to_0920_migration->migrate();

				$logs_cleaner_timer_to_0922_migration = $this->container['affilicious.common.migration.logs_cleaner_timer_to_0922'];
				$logs_cleaner_timer_to_0922_migration->migrate();

				$orphaned_product_variants_timer_migration = $this->container['affilicious.product.migration.orphaned_product_variants_timer_to_0922'];
				$orphaned_product_variants_timer_migration->migrate();
			}, 9999);
		}

		/**
		 * Register the plugin textdomain for internationalization.
		 *
		 * @hook plugins_loaded
		 * @since 0.5.1
		 */
		public function load_textdomain()
		{
			$dir = basename(dirname(__FILE__)) . '/languages/';
			load_plugin_textdomain(self::NAME, false, $dir);
		}

		/**
		 * Load some legacy code which will be removed in the future.
		 *
		 * @since 0.10
		 */
		public function load_legacy()
		{
			new Affilicious\Product\Meta_Box\Meta_Box_Manager();
		}

		/**
		 * Load the simple functions for an easier usage in templates.
		 *
		 * @since 0.5.1
		 */
		public function load_functions()
		{
			require_once(__DIR__ . '/functions.php');
		}

		/**
		 * Register the services for the dependency injection.
		 *
		 * @since 0.3
		 */
		public function load_services()
		{
			// Common services
			$this->container['affilicious.common.logger'] = function () {
				return new Affilicious\Common\Logger\Logger();
			};

			$this->container['affilicious.common.cleaner.logs'] = function ($c) {
				return new Affilicious\Common\Cleaner\Logs_Cleaner(
					$c['affilicious.common.logger']
				);
			};

			$this->container['affilicious.common.cleaner.logs_timer'] = function ($c) {
				return new Affilicious\Common\Cleaner\Logs_Cleaner_Timer(
					$c['affilicious.common.cleaner.logs']
				);
			};

			$this->container['affilicious.common.generator.slug'] = function () {
				return new Affilicious\Common\Generator\Wordpress\Wordpress_Slug_Generator();
			};

			$this->container['affilicious.common.generator.key'] = function () {
				return new Affilicious\Common\Generator\Carbon\Carbon_Key_Generator();
			};

			$this->container['affilicious.common.template.locator'] = function () {
				return new Affilicious\Common\Template\Template_Locator();
			};

			$this->container['affilicious.common.template.renderer'] = function ($c) {
				return new Affilicious\Common\Template\Template_Renderer(
					$c['affilicious.common.template.locator']
				);
			};

			$this->container['affilicious.common.filter.link_target'] = function () {
				return new Affilicious\Common\Filter\Link_Target_Filter();
			};

			$this->container['affilicious.common.filter.taxonomy_templates'] = function () {
				return new Affilicious\Common\Filter\Taxonomy_Templates_Filter();
			};

			$this->container['affilicious.common.setup.assets'] = function() {
				return new Affilicious\Common\Setup\Assets_Setup();
			};

			$this->container['affilicious.common.setup.image_size'] = function() {
				return new Affilicious\Common\Setup\Image_Size_Setup();
			};

			$this->container['affilicious.common.setup.logger_handler'] = function($c) {
				return new Affilicious\Common\Setup\Logger_Handler_Setup(
					$c['affilicious.common.logger']
				);
			};

			$this->container['affilicious.common.setup.logs_table'] = function($c) {
				return new Affilicious\Common\Setup\Logs_Table_Setup(
					$c['affilicious.common.table_creator.logs']
				);
			};

			$this->container['affilicious.common.table_creator.logs'] = function() {
				return new Affilicious\Common\Table_Creator\Logs_Table_Creator();
			};

			$this->container['affilicious.common.listener.create_blog'] = function($c) {
				return new Affilicious\Common\Listener\Create_Blog_Listener(
					$c['affilicious.common.table_creator.logs'],
					$c['affilicious.common.admin.license.timer']
				);
			};

			$this->container['affilicious.common.listener.drop_tables'] = function() {
				return new Affilicious\Common\Listener\Drop_Tables_Listener();
			};

			$this->container['affilicious.common.migration.non_existing_logs_table_to_0920'] = function ($c) {
				return new Affilicious\Common\Migration\Non_Existing_Logs_Table_To_0920_Migration(
					$c['affilicious.common.table_creator.logs']
				);
			};

			$this->container['affilicious.common.migration.logs_cleaner_timer_to_0922'] = function ($c) {
				return new Affilicious\Common\Migration\Logs_Cleaner_Timer_to_0922_Migration(
					$c['affilicious.common.cleaner.logs_timer']
				);
			};

            $this->container['affilicious.common.admin.setup.plugin_actions'] = function() {
                return new Affilicious\Common\Admin\Setup\Plugin_Actions_Setup();
            };

			$this->container['affilicious.common.admin.filter.footer_text'] = function () {
				return new Affilicious\Common\Admin\Filter\Footer_Text_Filter();
			};

			$this->container['affilicious.common.admin.setup.carbon'] = function () {
				return new Affilicious\Common\Admin\Setup\Carbon_Setup();
			};

			$this->container['affilicious.common.admin.page.addons'] = function () {
				return new Affilicious\Common\Admin\Page\Addons_Page();
			};

			$this->container['affilicious.common.admin.license.processor'] = function ($c) {
				return new Affilicious\Common\Admin\License\License_Processor(
					$c['affilicious.common.admin.license.manager']
				);
			};

			$this->container['affilicious.common.admin.license.manager'] = function () {
				return new Affilicious\Common\Admin\License\License_Manager();
			};

			$this->container['affilicious.common.admin.license.timer'] = function ($c) {
				return new Affilicious\Common\Admin\License\License_Timer(
					$c['affilicious.common.admin.license.manager']
				);
			};

			$this->container['affilicious.common.admin.system.info'] = function () {
				return new Affilicious\Common\Admin\System\System_Info();
			};

            $this->container['affilicious.common.admin.logs.logs'] = function () {
                return new Affilicious\Common\Admin\Logs\logs();
            };

            $this->container['affilicious.common.admin.ajax_handler.dismissed_notice'] = function () {
                return new Affilicious\Common\Admin\Ajax_Handler\Dismissed_Notice_Ajax_Handler();
            };

			$this->container['affilicious.common.admin.setup.license_handler'] = function ($c) {
				return new Affilicious\Common\Admin\Setup\License_Handler_Setup(
					$c['affilicious.common.admin.license.manager']
				);
			};

			$this->container['affilicious.common.admin.options.affilicious'] = function ($c) {
				return new Affilicious\Common\Admin\Options\Affilicious_Options(
					$c['affilicious.common.admin.license.manager'],
					$c['affilicious.common.admin.license.processor'],
					$c['affilicious.common.admin.system.info'],
                    $c['affilicious.common.admin.logs.logs'],
					$c['affilicious.common.template.renderer']
				);
			};

            $this->container['affilicious.common.admin.action.download_system_info'] = function ($c) {
                return new Affilicious\Common\Admin\Action\Download_System_Info_Action(
                    $c['affilicious.common.admin.system.info']
                );
            };

            $this->container['affilicious.common.admin.action.download_logs'] = function ($c) {
                return new Affilicious\Common\Admin\Action\Download_Logs_Action(
                    $c['affilicious.common.admin.logs.logs']
                );
            };

			$this->container['affilicious.common.admin.setup.assets'] = function() {
				return new Affilicious\Common\Admin\Setup\Assets_Setup();
			};

			$this->container['affilicious.common.admin.setup.download_recommendation'] = function() {
				return new Affilicious\Common\Admin\Setup\Download_Recommendation_Setup();
			};

			$this->container['affilicious.common.admin.notice.download_recommendation'] = function() {
				return new Affilicious\Common\Admin\Notice\Download_Recommendation_Notice();
			};

			// Provider services
			$this->container['affilicious.provider.setup.provider'] = function ($c) {
				return new Affilicious\Provider\Setup\Provider_Setup(
					$c['affilicious.provider.repository.provider']
				);
			};

			$this->container['affilicious.provider.repository.provider'] = function ($c) {
				return new Affilicious\Provider\Repository\Carbon\Carbon_Provider_Repository(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.provider.admin.notice.amazon_not_included_anymore'] = function () {
				return new Affilicious\Provider\Admin\Notice\Amazon_Not_Included_Anymore_Notice();
			};
			
			// Shop services
			$this->container['affilicious.shop.setup.shop_template'] = function () {
				return new Affilicious\Shop\Setup\Shop_Template_Setup();
			};

			$this->container['affilicious.shop.repository.shop_template'] = function () {
				return new Affilicious\Shop\Repository\Carbon\Carbon_Shop_Template_Repository();
			};

			$this->container['affilicious.shop.factory.shop_template'] = function ($c) {
				return new Affilicious\Shop\Factory\In_Memory\In_Memory_Shop_Template_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.shop.migration.post_to_term'] = function ($c) {
				return new Affilicious\Shop\Migration\Post_To_Term_Migration(
					$c['affilicious.shop.factory.shop_template'],
					$c['affilicious.shop.repository.shop_template']
				);
			};

			$this->container['affilicious.shop.migration.post_type'] = function () {
				return new Affilicious\Shop\Migration\Post_Type_Migration();
			};

			$this->container['affilicious.shop.migration.currency_code'] = function () {
				return new Affilicious\Shop\Migration\Currency_Code_Migration();
			};

			$this->container['affilicious.shop.admin.meta_box.shop_template'] = function($c) {
				return new Affilicious\Shop\Admin\Meta_Box\Shop_Template_Meta_Box(
					$c['affilicious.provider.repository.provider']
				);
			};

			$this->container['affilicious.shop.admin.filter.table_columns'] = function() {
				return new Affilicious\Shop\Admin\Filter\Table_Columns_Filter();
			};

			$this->container['affilicious.shop.admin.filter.table_rows'] = function($c) {
				return new Affilicious\Shop\Admin\Filter\Table_Rows_Filter(
					$c['affilicious.provider.repository.provider']
				);
			};

			// Detail services
			$this->container['affilicious.detail.setup.detail_template'] = function () {
				return new Affilicious\Detail\Setup\Detail_Template_Setup();
			};

			$this->container['affilicious.detail.factory.detail_template'] = function ($c) {
				return new Affilicious\Detail\Factory\In_Memory\In_Memory_Detail_Template_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.detail.repository.detail_template'] = function () {
				return new Affilicious\Detail\Repository\Carbon\Carbon_Detail_Template_Repository();
			};

			$this->container['affilicious.detail.migration.post_to_term'] = function ($c) {
				return new Affilicious\Detail\Migration\Post_To_Term_Migration(
					$c['affilicious.detail.factory.detail_template'],
					$c['affilicious.detail.repository.detail_template']
				);
			};

			$this->container['affilicious.detail.admin.meta_box.detail_template'] = function() {
				return new Affilicious\Detail\Admin\Meta_Box\Detail_Template_Meta_Box();
			};

			$this->container['affilicious.detail.admin.filter.table_columns'] = function() {
				return new Affilicious\Detail\Admin\Filter\Table_Columns_Filter();
			};

			$this->container['affilicious.detail.admin.filter.table_rows'] = function() {
				return new Affilicious\Detail\Admin\Filter\Table_Rows_Filter();
			};

			// Attribute services
			$this->container['affilicious.attribute.setup.attribute_template'] = function () {
				return new Affilicious\Attribute\Setup\Attribute_Template_Setup();
			};

			$this->container['affilicious.attribute.repository.attribute_template'] = function () {
				return new Affilicious\Attribute\Repository\Carbon\Carbon_Attribute_Template_Repository();
			};

			$this->container['affilicious.attribute.factory.attribute_template'] = function ($c) {
				return new Affilicious\Attribute\Factory\In_Memory\In_Memory_Attribute_Template_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.attribute.migration.post_to_term'] = function ($c) {
				return new Affilicious\Attribute\Migration\Post_To_Term_Migration(
					$c['affilicious.attribute.factory.attribute_template'],
					$c['affilicious.attribute.repository.attribute_template']
				);
			};

			$this->container['affilicious.attribute.admin.meta_box.attribute_template'] = function() {
				return new Affilicious\Attribute\Admin\Meta_Box\Attribute_Template_Meta_Box();
			};

			$this->container['affilicious.attribute.admin.filter.table_columns'] = function() {
				return new Affilicious\Attribute\Admin\Filter\Table_Columns_Filter();
			};

			$this->container['affilicious.attribute.admin.filter.table_rows'] = function() {
				return new Affilicious\Attribute\Admin\Filter\Table_Rows_Filter();
			};

			// Product services
			$this->container['affilicious.product.setup.product'] = function () {
				return new Affilicious\Product\Setup\Product_Setup();
			};

			$this->container['affilicious.product.setup.microdata'] = function () {
				return new Affilicious\Product\Setup\Microdata_Setup();
			};

			$this->container['affilicious.product.repository.product'] = function ($c) {
				return new Affilicious\Product\Repository\Carbon\Carbon_Product_Repository(
					$c['affilicious.common.generator.slug'],
					$c['affilicious.common.generator.key'],
					$c['affilicious.shop.repository.shop_template'],
					$c['affilicious.attribute.repository.attribute_template'],
					$c['affilicious.detail.repository.detail_template']
				);
			};

			$this->container['affilicious.product.factory.simple_product'] = function ($c) {
				return new Affilicious\Product\Factory\In_Memory\In_Memory_Simple_Product_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.product.factory.complex_product'] = function ($c) {
				return new Affilicious\Product\Factory\In_Memory\In_Memory_Complex_Product_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.product.factory.product_variant'] = function ($c) {
				return new Affilicious\Product\Factory\In_Memory\In_Memory_Product_Variant_Factory(
					$c['affilicious.common.generator.slug']
				);
			};

			$this->container['affilicious.product.filter.universal_box'] = function () {
				return new Affilicious\Product\Filter\Universal_Box_Filter();
			};

			$this->container['affilicious.product.filter.public_visibility'] = function () {
				return new Affilicious\Product\Filter\Public_Visibility_Filter();
			};

			$this->container['affilicious.product.filter.product_slug'] = function () {
				return new Affilicious\Product\Filter\Product_Slug_Filter();
			};

			$this->container['affilicious.product.filter.product_shops_meta_like_query'] = function () {
				return new Affilicious\Product\Filter\Product_Shops_Meta_Like_Query_Filter();
			};

			$this->container['affilicious.product.listener.changed_product_status'] = function () {
				return new Affilicious\Product\Listener\Changed_Product_Status_Listener();
			};

			$this->container['affilicious.product.listener.saved_complex_product'] = function ($c) {
				return new Affilicious\Product\Listener\Saved_Complex_Product_Listener(
					$c['affilicious.product.repository.product']
				);
			};

			$this->container['affilicious.product.listener.create_blog'] = function($c) {
				return new Affilicious\Product\Listener\Create_Blog_Listener(
					$c['affilicious.product.update.semaphore'],
					$c['affilicious.product.update.timer']
				);
			};

			$this->container['affilicious.product.listener.deleted_complex_product'] = function ($c) {
				return new Affilicious\Product\Listener\Deleted_Complex_Product_Listener(
					$c['affilicious.product.repository.product']
				);
			};

			$this->container['affilicious.product.cleaner.orphaned_product_variants'] = function ($c) {
				return new Affilicious\Product\Cleaner\Orphaned_Product_Variants_Cleaner(
					$c['affilicious.common.logger']
				);
			};

			$this->container['affilicious.product.cleaner.orphaned_product_variants_timer'] = function ($c) {
				return new Affilicious\Product\Cleaner\Orphaned_Product_Variants_Cleaner_Timer(
					$c['affilicious.product.cleaner.orphaned_product_variants']
				);
			};

			$this->container['affilicious.product.setup.custom_taxonomies'] = function () {
				return new Affilicious\Product\Setup\Custom_Taxonomies_Setup();
			};

			$this->container['affilicious.product.setup.canonical'] = function () {
				return new Affilicious\Product\Setup\Canonical_Setup();
			};

			$this->container['affilicious.product.setup.admin_bar'] = function () {
				return new Affilicious\Product\Setup\Admin_Bar_Setup();
			};

			$this->container['affilicious.product.filter.disable_complex_products_for_query'] = function () {
				return new Affilicious\Product\Filter\Disable_Complex_Products_For_Query_Filter();
			};

			$this->container['affilicious.product.listener.edited_shop_template'] = function ($c) {
				return new Affilicious\Product\Listener\Edited_Shop_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.listener.edited_attribute_template'] = function ($c) {
				return new Affilicious\Product\Listener\Edited_Attribute_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.listener.edited_detail_template'] = function ($c) {
				return new Affilicious\Product\Listener\Edited_Detail_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.listener.deleted_shop_template'] = function ($c) {
				return new Affilicious\Product\Listener\Deleted_Shop_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.listener.deleted_attribute_template'] = function ($c) {
				return new Affilicious\Product\Listener\Deleted_Attribute_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.listener.deleted_detail_template'] = function ($c) {
				return new Affilicious\Product\Listener\Deleted_Detail_Template_Listener(
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.setup.slug_rewrite'] = function ($c) {
				return new Affilicious\Product\Setup\Slug_Rewrite_Setup(
					$c['affilicious.product.setup.product'],
					$c['affilicious.product.setup.custom_taxonomies']
				);
			};

			$this->container['affilicious.product.update.timer'] = function ($c) {
				return new Affilicious\Product\Update\Update_Timer(
					$c['affilicious.product.update.manager'],
					$c['affilicious.product.update.semaphore']
				);
			};

			$this->container['affilicious.product.update.task.broker'] = function($c) {
				return new Affilicious\Product\Update\Task\Broker\Update_Task_Broker(
					$c['affilicious.shop.repository.shop_template'],
					$c['affilicious.provider.repository.provider']
				);
			};

			$this->container['affilicious.product.update.manager'] = function ($c) {
				return new Affilicious\Product\Update\Update_Manager(
					$c['affilicious.product.update.task.broker'],
					$c['affilicious.product.repository.product'],
					$c['affilicious.provider.repository.provider']
				);
			};

			$this->container['affilicious.product.update.semaphore'] = function ($c) {
				return new Affilicious\Product\Update\Update_Semaphore(
					$c['affilicious.common.logger']
				);
			};

			$this->container['affilicious.product.setup.update_worker'] = function ($c) {
				return new Affilicious\Product\Setup\Update_Worker_Setup(
					$c['affilicious.product.update.manager']
				);
			};
			
			$this->container['affilicious.product.setup.update_queue'] = function ($c) {
				return new Affilicious\Product\Setup\Update_Queue_Setup(
					$c['affilicious.product.update.task.broker']
				);
			};

			$this->container['affilicious.product.customizer.universal_box'] = function() {
				return new Affilicious\Product\Customizer\Universal_Box_Customizer();
			};

			$this->container['affilicious.product.migration.post_type'] = function () {
				return new Affilicious\Product\Migration\Post_Type_Migration();
			};

			$this->container['affilicious.product.migration.product_slugs_to_0818'] = function () {
				return new Affilicious\Product\Migration\Product_Slugs_To_0818_Migration();
			};

			$this->container['affilicious.product.migration.product_variant_terms_to_0820'] = function ($c) {
				return new Affilicious\Product\Migration\Product_Variant_Terms_To_0820_Migration(
					$c['affilicious.product.repository.product']
				);
			};

			$this->container['affilicious.product.migration.affiliate_product_id_to_090'] = function () {
				return new Affilicious\Product\Migration\Affiliate_Product_Id_To_090_Migration();
			};

			$this->container['affilicious.product.migration.product_updates_to_0920'] = function ($c) {
				return new Affilicious\Product\Migration\Product_Updates_To_0920_Migration(
					$c['affilicious.product.update.semaphore']
				);
			};

			$this->container['affilicious.product.migration.variants'] = function ($c) {
				return new Affilicious\Product\Migration\Variants_Migration(
					$c['affilicious.product.repository.product'],
					$c['affilicious.attribute.repository.attribute_template'],
					$c['affilicious.shop.repository.shop_template'],
					$c['affilicious.product.factory.product_variant']
				);
			};

			$this->container['affilicious.product.migration.shops'] = function ($c) {
				return new Affilicious\Product\Migration\Shops_Migration(
					$c['affilicious.product.repository.product'],
					$c['affilicious.shop.repository.shop_template']
				);
			};

			$this->container['affilicious.product.migration.clean_variants'] = function () {
				return new Affilicious\Product\Migration\Clean_Variants_Migration();
			};

			$this->container['affilicious.product.migration.variant_inherit_status'] = function () {
				return new Affilicious\Product\Migration\Variant_Inherit_Status_Migration();
			};

			$this->container['affilicious.product.migration.product_slug'] = function () {
				return new Affilicious\Product\Migration\Product_Slug_Migration();
			};

			$this->container['affilicious.product.migration.details'] = function ($c) {
				return new Affilicious\Product\Migration\Details_Migration(
					$c['affilicious.product.repository.product'],
					$c['affilicious.detail.repository.detail_template']
				);
			};

			$this->container['affilicious.product.migration.tags_to_090'] = function($c) {
				return new Affilicious\Product\Migration\Tags_To_090_Migration(
					$c['affilicious.detail.repository.detail_template'],
					$c['affilicious.attribute.repository.attribute_template']
				);
			};

			$this->container['affilicious.product.migration.orphaned_product_variants_timer_to_0922'] = function($c) {
				return new Affilicious\Product\Migration\Orphaned_Product_Variants_Cleaner_Timer_to_0922_Migration(
					$c['affilicious.product.cleaner.orphaned_product_variants_timer']
				);
			};

			$this->container['affilicious.product.admin.setup.import_page'] = function () {
				return new Affilicious\Product\Admin\Setup\Import_Page_Setup();
			};

			$this->container['affilicious.product.admin.filter.table_columns'] = function () {
				return new Affilicious\Product\Admin\Filter\Table_Columns_Filter();
			};

			$this->container['affilicious.product.admin.filter.table_rows'] = function () {
				return new Affilicious\Product\Admin\Filter\Table_Rows_Filter();
			};

			$this->container['affilicious.product.admin.meta_box.product'] = function($c) {
				return new Affilicious\Product\Admin\Meta_Box\Product_Meta_Box(
					$c['affilicious.shop.repository.shop_template'],
					$c['affilicious.attribute.repository.attribute_template'],
					$c['affilicious.detail.repository.detail_template'],
					$c['affilicious.common.generator.key']
				);
			};

			$this->container['affilicious.product.admin.options.product'] = function () {
				return new Affilicious\Product\Admin\Options\Product_Options();
			};

			$this->container['affilicious.product.admin.filter.table_content'] = function () {
				return new Affilicious\Product\Admin\Filter\Table_Content_Filter();
			};

			$this->container['affilicious.product.admin.filter.table_count'] = function () {
				return new Affilicious\Product\Admin\Filter\Table_Count_Filter();
			};

			$this->container['affilicious.product.admin.filter.menu_order'] = function() {
				return new Affilicious\Product\Admin\Filter\Menu_Order_Filter();
			};
		}

		/**
		 * Register all of the hooks related to the public-facing functionality.
		 *
		 * @since 0.3
		 */
		public function register_public_hooks()
		{
			// Text domains
			add_action('plugins_loaded', array($this, 'load_textdomain'));

			// Setups
			$product_setup = $this->container['affilicious.product.setup.product'];
			$custom_product_taxonomies_setup = $this->container['affilicious.product.setup.custom_taxonomies'];
			$shop_template_setup = $this->container['affilicious.shop.setup.shop_template'];
			$attribute_template_setup = $this->container['affilicious.attribute.setup.attribute_template'];
			$detail_template_setup = $this->container['affilicious.detail.setup.detail_template'];
			$slug_rewrite_setup = $this->container['affilicious.product.setup.slug_rewrite'];
			$provider_setup = $this->container['affilicious.provider.setup.provider'];
			$logger_handler_setup = $this->container['affilicious.common.setup.logger_handler'];
			$update_queue_setup = $this->container['affilicious.product.setup.update_queue'];
			$update_worker_setup = $this->container['affilicious.product.setup.update_worker'];
			$assets_setup = $this->container['affilicious.common.setup.assets'];
			$canonical_setup = $this->container['affilicious.product.setup.canonical'];
			$image_size_setup = $this->container['affilicious.common.setup.image_size'];
			$admin_bar_setup = $this->container['affilicious.product.setup.admin_bar'];
			$license_handler_setup = $this->container['affilicious.common.admin.setup.license_handler'];
			$microdata_setup = $this->container['affilicious.product.setup.microdata'];
			add_action('init', array($product_setup, 'init'), 0);
			add_action('init', array($custom_product_taxonomies_setup, 'init'), 0);
			add_action('init', array($shop_template_setup, 'init'), 0);
			add_action('init', array($attribute_template_setup, 'init'), 0);
			add_action('init', array($detail_template_setup, 'init'), 0);
			add_action('init', array($slug_rewrite_setup, 'run'), 1);
			add_action('added_option', array($slug_rewrite_setup, 'prepare'), 80, 1);
			add_action('updated_option', array($slug_rewrite_setup, 'prepare'), 80, 1);
			add_action('init', array($provider_setup, 'init'), 5);
			add_action('init', array($logger_handler_setup, 'init'), 5);
			add_filter('aff_provider_after_init', array($update_queue_setup, 'init'));
			add_action('init', array($update_worker_setup, 'init'), 5);
			add_action('wp_enqueue_scripts', array($assets_setup, 'add_styles'));
			add_action('wp_enqueue_scripts', array($assets_setup, 'add_scripts'));
			add_action('wp_head', array($canonical_setup, 'init'));
			add_action('init', array($image_size_setup, 'init'));
			add_action('admin_bar_menu', array($admin_bar_setup, 'init'), 99);
			add_action('init', array($license_handler_setup, 'init'), 15);
			add_action('wp_head', array($microdata_setup, 'init'));

			// Filters
			$product_slug_filter = $this->container['affilicious.product.filter.product_slug'];
			$product_public_visibility_filter = $this->container['affilicious.product.filter.public_visibility'];
			$universal_box_filter = $this->container['affilicious.product.filter.universal_box'];
			$disable_complex_products_for_query_filter = $this->container['affilicious.product.filter.disable_complex_products_for_query'];
			$link_target_filter = $this->container['affilicious.common.filter.link_target'];
			$product_shops_meta_like_query_filter = $this->container['affilicious.product.filter.product_shops_meta_like_query'];
			$taxonomy_templates_filter = $this->container['affilicious.common.filter.taxonomy_templates'];
			add_filter('aff_product_init_args', array($product_slug_filter, 'filter'));
			add_filter('aff_product_init_args', array($product_public_visibility_filter, 'filter'));
			add_filter('the_content', array($universal_box_filter, 'filter'));
			add_action('pre_get_posts', array($disable_complex_products_for_query_filter, 'filter'));
			add_filter('tiny_mce_before_init', array($link_target_filter, 'filter'));
			add_filter('posts_where' , array($product_shops_meta_like_query_filter, 'filter'), 10, 2);
			add_filter('taxonomy_template_hierarchy', array($taxonomy_templates_filter, 'filter'));

			// Listeners
			$create_blog_listener = $this->container['affilicious.common.listener.create_blog'];
			$drop_tables_listener = $this->container['affilicious.common.listener.drop_tables'];
			$product_create_blog_listener = $this->container['affilicious.product.listener.create_blog'];
			$saved_complex_product_listener = $this->container['affilicious.product.listener.saved_complex_product'];
			$deleted_complex_product_listener = $this->container['affilicious.product.listener.deleted_complex_product'];
			$changed_product_status_listener = $this->container['affilicious.product.listener.changed_product_status'];
			$edited_shop_template_listener = $this->container['affilicious.product.listener.edited_shop_template'];
			$edited_attribute_template_listener = $this->container['affilicious.product.listener.edited_attribute_template'];
			$edited_detail_template_listener = $this->container['affilicious.product.listener.edited_detail_template'];
			$deleted_shop_template_listener = $this->container['affilicious.product.listener.deleted_shop_template'];
			$deleted_attribute_template_listener = $this->container['affilicious.product.listener.deleted_attribute_template'];
			$deleted_detail_template_listener = $this->container['affilicious.product.listener.deleted_detail_template'];
			add_action('wpmu_new_blog', array($create_blog_listener, 'listen'), 10, 1);
			add_action('wpmu_drop_tables', array($drop_tables_listener, 'listen'), 10, 2);
			add_action('wpmu_new_blog', array($product_create_blog_listener, 'listen'), 10, 1);
			add_action('carbon_after_save_post_meta', array($saved_complex_product_listener, 'listen'), 10, 3);
			add_action('delete_post', array($deleted_complex_product_listener, 'listen'));
			add_action('save_post', array($changed_product_status_listener, 'listen'), 10, 3);
			add_action('edit_aff_shop_tmpl', array($edited_shop_template_listener, 'before_edit'), 10, 1);
			add_action('edit_aff_attribute_tmpl', array($edited_attribute_template_listener, 'before_edit'), 10, 1);
			add_action('edit_aff_detail_tmpl', array($edited_detail_template_listener, 'before_edit'), 10, 1);
			add_action('edited_aff_shop_tmpl', array($edited_shop_template_listener, 'after_edit'), 10, 1);
			add_action('edited_aff_attribute_tmpl', array($edited_attribute_template_listener, 'after_edit'), 10, 1);
			add_action('edited_aff_detail_tmpl', array($edited_detail_template_listener, 'after_edit'), 10, 1);
			add_action('delete_aff_shop_tmpl', array($deleted_shop_template_listener, 'delete'), 10, 3);
			add_action('delete_aff_attribute_tmpl', array($deleted_attribute_template_listener, 'delete'), 10, 3);
			add_action('delete_aff_detail_tmpl', array($deleted_detail_template_listener, 'delete'), 10, 3);

			// Licenses
			$license_timer = $this->container['affilicious.common.admin.license.timer'];
			add_action('aff_common_admin_license_run_checks_daily', array($license_timer, 'run_checks_daily'));

			// Product updates
			$update_timer = $this->container['affilicious.product.update.timer'];
			add_action('aff_product_update_run_tasks_hourly', array($update_timer, 'run_tasks_hourly'));
			add_action('aff_product_update_run_tasks_twice_daily', array($update_timer, 'run_tasks_twice_daily'));
			add_action('aff_product_update_run_tasks_daily', array($update_timer, 'run_tasks_daily'));

			// Customizers
			$universal_box_customizer = $this->container['affilicious.product.customizer.universal_box'];
			add_action('customize_register', array($universal_box_customizer, 'register'));
			add_action('wp_enqueue_scripts', array($universal_box_customizer, 'render'));

			// Cleaners
			$logs_cleaner_timer = $this->container['affilicious.common.cleaner.logs_timer'];
			$orphaned_product_variants_timer = $this->container['affilicious.product.cleaner.orphaned_product_variants_timer'];
			add_action('aff_common_cleaner_logs_clean_up_daily', array($logs_cleaner_timer, 'clean_up_daily'));
			add_action('aff_product_cleaner_orphaned_product_variants_clean_up_daily', array($orphaned_product_variants_timer, 'clean_up_daily'));

			// Hook into this action if you want to create custom extensions or themes with the dependency injection container.
			do_action('aff_hooks');

			add_action('init', function() {
				/** @deprecated 1.1 */
				do_action('aff_init');

				/** @deprecated 1.0 */
				do_action('affilicious_init');
			});
		}

		/**
		 * Register all of the hooks related to the admin area functionality.
		 *
		 * @since 0.3
		 */
		public function register_admin_hooks()
		{
			// Setups
			$carbon_fields_setup = $this->container['affilicious.common.admin.setup.carbon'];
			$import_page_setup = $this->container['affilicious.product.admin.setup.import_page'];
			add_action('after_setup_theme', array($carbon_fields_setup, 'init'), 15);
			add_action('admin_menu', array($import_page_setup, 'init'));
			add_filter('parent_file', array($import_page_setup, 'highlighted_url'));

			// Meta boxes
			$product_meta_box = $this->container['affilicious.product.admin.meta_box.product'];
			$shop_template_meta_box = $this->container['affilicious.shop.admin.meta_box.shop_template'];
			$attribute_template_meta_box = $this->container['affilicious.attribute.admin.meta_box.attribute_template'];
			$detail_template_meta_box = $this->container['affilicious.detail.admin.meta_box.detail_template'];
			$assets_setup = $this->container['affilicious.common.admin.setup.assets'];
			$plugin_actions_setup = $this->container['affilicious.common.admin.setup.plugin_actions'];
			add_action('init', array($product_meta_box, 'render'), 10);
			add_action('init', array($shop_template_meta_box, 'render'), 10);
			add_action('init', array($attribute_template_meta_box, 'render'), 10);
			add_action('init', array($detail_template_meta_box, 'render'), 10);
			add_action('admin_enqueue_scripts', array($assets_setup, 'add_styles'));
			add_action('admin_enqueue_scripts', array($assets_setup, 'add_scripts'));
			add_filter('plugin_action_links_' . AFFILICIOUS_BASE_NAME, array($plugin_actions_setup, 'init'));

			// Notices
			$download_recommendation_notice = $this->container['affilicious.common.admin.notice.download_recommendation'];
			$amazon_not_included_anymore = $this->container['affilicious.provider.admin.notice.amazon_not_included_anymore'];
			add_action('admin_notices', array($download_recommendation_notice, 'render'));
			add_action('admin_notices', array($amazon_not_included_anymore, 'render'));

            // Actions
            $download_system_info_action = $this->container['affilicious.common.admin.action.download_system_info'];
            $download_logs_action = $this->container['affilicious.common.admin.action.download_logs'];
            add_action('admin_action_aff_download_system_info', array($download_system_info_action, 'handle'));
            add_action('admin_action_aff_download_logs', array($download_logs_action, 'handle'));

			// Filters
			$product_admin_table_content_filter = $this->container['affilicious.product.admin.filter.table_content'];
			$product_admin_table_count_filter = $this->container['affilicious.product.admin.filter.table_count'];
			$product_admin_table_columns_filter = $this->container['affilicious.product.admin.filter.table_columns'];
			$product_admin_table_rows_filter = $this->container['affilicious.product.admin.filter.table_rows'];
			$attribute_template_admin_table_columns_filter = $this->container['affilicious.attribute.admin.filter.table_columns'];
			$attribute_template_admin_table_rows_filter = $this->container['affilicious.attribute.admin.filter.table_rows'];
			$detail_template_admin_table_columns_filter = $this->container['affilicious.detail.admin.filter.table_columns'];
			$detail_template_admin_table_rows_filter = $this->container['affilicious.detail.admin.filter.table_rows'];
			$shop_template_admin_table_columns_filter = $this->container['affilicious.shop.admin.filter.table_columns'];
			$shop_template_admin_table_rows_filter = $this->container['affilicious.shop.admin.filter.table_rows'];
			$product_admin_menu_order_filter = $this->container['affilicious.product.admin.filter.menu_order'];
			$admin_footer_text_filter = $this->container['affilicious.common.admin.filter.footer_text'];
			add_action('pre_get_posts', array($product_admin_table_content_filter, 'filter'));
			add_filter("views_edit-aff_product", array($product_admin_table_count_filter, 'filter'), 10, 1);
			add_filter('manage_aff_product_posts_columns', array($product_admin_table_columns_filter, 'filter'));
			add_filter('manage_aff_product_posts_custom_column', array($product_admin_table_rows_filter, 'filter'), 10, 2);
			add_filter('manage_edit-aff_attribute_tmpl_columns',  array($attribute_template_admin_table_columns_filter, 'filter'));
			add_filter('manage_aff_attribute_tmpl_custom_column', array($attribute_template_admin_table_rows_filter, 'filter'), 15, 3);
			add_filter('manage_edit-aff_detail_tmpl_columns',  array($detail_template_admin_table_columns_filter, 'filter'));
			add_filter('manage_aff_detail_tmpl_custom_column', array($detail_template_admin_table_rows_filter, 'filter'), 15, 3);
			add_filter('manage_edit-aff_shop_tmpl_columns',  array($shop_template_admin_table_columns_filter, 'filter'));
			add_filter('manage_aff_shop_tmpl_custom_column', array($shop_template_admin_table_rows_filter, 'filter'), 15, 3);
			add_filter('custom_menu_order', array($product_admin_menu_order_filter, 'filter'));
			add_filter('admin_footer_text', array($admin_footer_text_filter, 'filter'));

			// Options
			$affilicious_options = $this->container['affilicious.common.admin.options.affilicious'];
			$product_options = $this->container['affilicious.product.admin.options.product'];
			add_action('init', array($affilicious_options, 'render'), 15);
			add_action('init', array($product_options, 'render'), 20);

			// Pages
			$addons_page = $this->container['affilicious.common.admin.page.addons'];
			add_action('admin_menu', array($addons_page, 'init'), 100);

			// Ajax handlers
            $dismissed_notice_ajax_handler = $this->container['affilicious.common.admin.ajax_handler.dismissed_notice'];
			add_action('wp_ajax_aff_dismissed_notice', array($dismissed_notice_ajax_handler , 'handle'));

			// Hook into this action if you want to create custom extensions or themes with the dependency injection container.
			do_action('aff_admin_hooks');

			// Add a custom affilicious admin init hook.
			add_action('admin_init', function() {
				/** @deprecated 1.1 */
				do_action('aff_admin_init');

				/** @deprecated 1.0 */
				do_action('affilicious_admin_init');
			});
		}
	}
}

/**
 * Run the Affilicious plugin.
 *
 * @since 0.8.11
 */
function aff_run_plugin()
{
	$affilicious = Affilicious::get_instance();
	$affilicious->run();
}

aff_run_plugin();
