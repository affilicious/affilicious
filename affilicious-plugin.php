<?php
/**
 * Plugin Name: Affilicious
 * Description: Manage affiliate products in Wordpress with price comparisons, automatically updated shops, product variants and much more.
 * Version: 0.7
 * Author: Affilicious Team
 * Author URI: https://affilicioustheme.de/
 * Plugin URI: https://affilicioustheme.de/downloads/affilicious/
 * License: GPL-2.0 or later
 * Requires at least: 4.5
 * Tested up to: 4.6
 * Text Domain: affilicious
 * Domain Path: /languages/
 *
 * Affilicious Plugin
 * Copyright (C) 2016, Affilicious - support@affilicioustheme.de
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
use Pimple\Container;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

define('AFFILICIOUS_PLUGIN_ROOT_DIR', plugin_dir_path(__FILE__));
define('AFFILICIOUS_PLUGIN_BASE_NAME', plugin_basename(__FILE__));

class Affilicious_Plugin
{
    const PLUGIN_NAME = 'affilicious';
    const PLUGIN_VERSION = '0.7';
    const PLUGIN_MIN_PHP_VERSION = '5.6';
    const PLUGIN_NAMESPACE = 'Affilicious\\';
    const PLUGIN_TESTS_NAMESPACE = 'Affilicious\\Tests\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_TESTS_DIR = 'tests/';
    const PLUGIN_LANGUAGE_DIR = 'languages/';
    const PLUGIN_STORE_URL = 'http://affilicioustheme.de';
    const PLUGIN_ITEM_NAME = 'Affilicious';
    const PLUGIN_LICENSE_KEY = 'e90a6d1a115da24a292fe0300afc402a';
    const PLUGIN_AUTHOR = 'Affilicious Team';

    /**
     * Stores the singleton instance
     *
     * @since 0.3
     * @var Affilicious_Plugin
     */
    private static $instance;

    /**
     * Register all services and parameters for the pimple dependency injection
     *
     * @see http://pimple.sensiolabs.org
     * @var Container
     */
    private $container;

    /**
     * Get the instance of the affilicious plugin
     *
     * @since 0.3
     * @return Affilicious_Plugin
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new Affilicious_Plugin();
        }

        return self::$instance;
    }

    /**
     * Get the root url to the plugin.
     *
     * @since 0.7
     * @return string
     */
    public static function get_root_url()
    {
        return plugin_dir_url(__FILE__);
    }

    /**
     * Get the root path to the plugin.
     *
     * @since 0.7
     * @return string
     */
    public static function get_root_path()
    {
        return plugin_dir_path(__FILE__);
    }

    /**
     * Get the root url to the plugin.
     *
     * @deprecated
     * @since 0.3
     * @return string
     */
    public static function get_root_dir()
    {
        return self::get_root_url();
    }

    /**
     * Prepare the plugin with for usage with Wordpress and namespaces
     *
     * @since 0.3
     */
    private function __construct()
    {
        if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
            require(__DIR__ . '/vendor/autoload.php');
        }

        spl_autoload_register(array($this, 'autoload'));

        $this->container = new Container();
    }

    /**
     * Get a reference to the dependency injection container
     *
     * @see http://pimple.sensiolabs.org
     * @since 0.3
     * @return Container
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
		register_activation_hook( __FILE__, array($this, 'activate'));
		register_deactivation_hook( __FILE__, array($this, 'deactivate'));

		$this->load_includes();
		$this->load_functions();
		$this->load_services();
		$this->register_public_hooks();
		$this->register_admin_hooks();

        // TODO: This old legacy class will be removed later
		new \Affilicious\Product\Presentation\Meta_Box\Meta_Box_Manager();

		// We have to call the container to the run code inside
		$this->container['affilicious.common.application.setup.carbon'];
	}

    /**
     * Update the plugin with the help of the Software Licensing for Easy Digital Downloads
     *
     * @since 0.3
     */
    public function update()
    {
        new \EDD_SL_Plugin_Updater(self::PLUGIN_STORE_URL, __FILE__, array(
            'version' => self::PLUGIN_VERSION,
            'license' => self::PLUGIN_LICENSE_KEY,
            'item_name' => self::PLUGIN_ITEM_NAME,
            'author' => self::PLUGIN_AUTHOR,
        ));
    }

    /**
     * Make namespaces compatible with the source code of this plugin
     *
     * @since 0.3
     * @param string $class
     */
    public function autoload($class)
    {
        $prefix = self::PLUGIN_NAMESPACE;
        if (stripos($class, $prefix) === false) {
            return;
        }

        $file_path = str_ireplace(self::PLUGIN_NAMESPACE, '', $class) . '.php';
        $file_path = strtolower($file_path);
        $file_path = str_replace('_', '-', $file_path);

        $test_prefix = self::PLUGIN_TESTS_NAMESPACE;
        if (stripos($class, $test_prefix) !== false) {
            $file_path = __DIR__ . '/' . $file_path;
        } else {
            $file_path = __DIR__ . '/' . self::PLUGIN_SOURCE_DIR . $file_path;
        }

        $file_path = str_replace('\\', DIRECTORY_SEPARATOR, $file_path);

        /** @noinspection PhpIncludeInspection */
        include_once($file_path);
    }

    /**
     * The code that runs during plugin activation.
     *
     * @since 0.3
     */
    public function activate()
    {
        // Check the PHP version requirement
        if(!version_compare(phpversion(), self::PLUGIN_MIN_PHP_VERSION, '>=')) {
            deactivate_plugins(AFFILICIOUS_PLUGIN_BASE_NAME);

            $this->load_textdomain();
            wp_die(sprintf(
                __('The Affilicious Plugin requires at least the PHP Version %s to reveal the full potential. Please switch the PHP version in your hosting provider.', 'affilicious'),
                self::PLUGIN_MIN_PHP_VERSION
            ));
        }

        $license_manager = $this->container['affilicious.common.application.license.manager'];
        $license_manager->activate(self::PLUGIN_ITEM_NAME, self::PLUGIN_LICENSE_KEY);

        $product_post_type_migration = $this->container['affilicious.product.application.migration.post_type'];
        $product_post_type_migration->migrate();

        $shop_post_type_migration = $this->container['affilicious.shop.application.migration.post_type'];
        $shop_post_type_migration->migrate();

        $currency_code_migration = $this->container['affilicious.shop.application.migration.currency_code'];
        $currency_code_migration->migrate();

        $slug_rewrite_setup = $this->container['affilicious.product.presentation.setup.slug_rewrite'];
        $slug_rewrite_setup->activate();

        $update_timer = $this->container['affilicious.product.application.update.timer'];
        $update_timer->activate();
    }

    /**
     * The code that runs during plugin deactivation.
     *
     * @since 0.3
     */
    public function deactivate()
    {
        $license_manager = $this->container['affilicious.common.application.license.manager'];
        $license_manager->deactivate(self::PLUGIN_ITEM_NAME, self::PLUGIN_LICENSE_KEY);

        $slug_rewrite_setup = $this->container['affilicious.product.presentation.setup.slug_rewrite'];
        $slug_rewrite_setup->deactivate();

        $update_timer = $this->container['affilicious.product.application.update.timer'];
        $update_timer->deactivate();
    }

    /**
     * Register the services for the dependency injection
     *
     * @since 0.3
     */
    public function load_services()
    {
        $this->container['affilicious.common.application.license.manager'] = function () {
            return new \Affilicious\Common\Application\License\EDD_License_Manager();
        };

        $this->container['affilicious.common.application.setup.asset'] = function () {
            return new \Affilicious\Common\Presentation\Setup\Asset_Setup();
        };

        $this->container['affilicious.common.application.setup.carbon'] = function () {
            return new \Affilicious\Common\Presentation\Setup\Carbon_Setup();
        };

        $this->container['affilicious.common.application.setup.feedback'] = function () {
            return new \Affilicious\Common\Presentation\Setup\Feedback_Setup();
        };

        $this->container['affilicious.product.infrastructure.repository.product'] = function ($c) {
            return new \Affilicious\Product\Infrastructure\Repository\Carbon\Carbon_Product_Repository(
                $c['affilicious.product.infrastructure.factory.review'],
                $c['affilicious.detail.infrastructure.factory.detail_group'],
                $c['affilicious.attribute.infrastructure.factory.attribute_group'],
                $c['affilicious.shop.infrastructure.factory.shop']
            );
        };

        $this->container['affilicious.product.infrastructure.factory.simple_product'] = function () {
            return new \Affilicious\Product\Infrastructure\Factory\In_Memory\In_Memory_Simple_Product_Factory();
        };

        $this->container['affilicious.product.infrastructure.factory.complex_product'] = function () {
            return new \Affilicious\Product\Infrastructure\Factory\In_Memory\In_Memory_Complex_Product_Factory();
        };

        $this->container['affilicious.product.infrastructure.factory.product_variant'] = function () {
            return new \Affilicious\Product\Infrastructure\Factory\In_Memory\In_Memory_Product_Variant_Factory();
        };

        $this->container['affilicious.detail.infrastructure.factory.detail_group'] = function ($c) {
            return new \Affilicious\Detail\Infrastructure\Factory\In_Memory\In_Memory_Detail_Group_Factory(
                $c['affilicious.detail.infrastructure.repository.detail_template_group']
            );
        };

        $this->container['affilicious.attribute.infrastructure.factory.attribute_group'] = function ($c) {
            return new \Affilicious\Attribute\Infrastructure\Factory\In_Memory\In_Memory_Attribute_Group_Factory(
                $c['affilicious.attribute.infrastructure.repository.attribute_template_group']
            );
        };

        $this->container['affilicious.shop.infrastructure.factory.shop'] = function ($c) {
            return new \Affilicious\Shop\Infrastructure\Factory\In_Memory\In_Memory_Shop_Factory(
                $c['affilicious.shop.infrastructure.repository.shop_template']
            );
        };

        $this->container['affilicious.shop.infrastructure.repository.shop_template'] = function ($c) {
            return new \Affilicious\Shop\Infrastructure\Repository\Carbon\Carbon_Shop_Template_Repository(
                $c['affilicious.shop.infrastructure.repository.provider']
            );
        };

        $this->container['affilicious.shop.application.setup.provider'] = function ($c) {
            return new \Affilicious\Shop\Application\Setup\Provider_Setup(
                $c['affilicious.shop.infrastructure.repository.provider'],
                $c['affilicious.shop.infrastructure.factory.provider.amazon']
            );
        };

        $this->container['affilicious.shop.application.setup.amazon_provider'] = function ($c) {
            return new \Affilicious\Shop\Application\Setup\Amazon_Provider_Setup(
                $c['affilicious.shop.infrastructure.factory.provider.amazon']
            );
        };

        $this->container['affilicious.shop.infrastructure.repository.provider'] = function () {
            return new \Affilicious\Shop\Infrastructure\Repository\In_Memory\In_Memory_Provider_Repository();
        };

        $this->container['affilicious.shop.infrastructure.factory.provider.amazon'] = function () {
            return new \Affilicious\Shop\Infrastructure\Factory\In_Memory\In_Memory_Amazon_Provider_Factory();
        };

        $this->container['affilicious.product.infrastructure.factory.review'] = function () {
            return new \Affilicious\Product\Infrastructure\Factory\In_Memory\In_Memory_Review_Factory();
        };

        $this->container['affilicious.shop.infrastructure.factory.shop_template'] = function () {
            return new \Affilicious\Shop\Infrastructure\Factory\In_Memory\In_Memory_Shop_Template_Factory();
        };

        $this->container['affilicious.detail.infrastructure.repository.detail_template_group'] = function ($c) {
            return new \Affilicious\Detail\Infrastructure\Repository\Carbon\Carbon_Detail_Template_Group_Repository(
                $c['affilicious.detail.infrastructure.factory.detail_template_group'],
                $c['affilicious.detail.infrastructure.factory.detail_template']
            );
        };

        $this->container['affilicious.detail.infrastructure.factory.detail_template_group'] = function () {
            return new \Affilicious\Detail\Infrastructure\Factory\In_Memory\In_Memory_Detail_Template_Group_Factory();
        };

        $this->container['affilicious.detail.infrastructure.factory.detail_template'] = function () {
            return new \Affilicious\Detail\Infrastructure\Factory\In_Memory\In_Memory_Detail_Template_Factory();
        };

        $this->container['affilicious.attribute.infrastructure.repository.attribute_template_group'] = function ($c) {
            return new \Affilicious\Attribute\Infrastructure\Repository\Carbon\Carbon_Attribute_Template_Group_Repository(
                $c['affilicious.attribute.infrastructure.factory.attribute_template_group'],
                $c['affilicious.attribute.infrastructure.factory.attribute_template']
            );
        };

        $this->container['affilicious.attribute.infrastructure.factory.attribute_template'] = function () {
            return new \Affilicious\Attribute\Infrastructure\Factory\In_Memory\In_Memory_Attribute_Template_Factory();
        };

        $this->container['affilicious.attribute.infrastructure.factory.attribute_template_group'] = function () {
            return new \Affilicious\Attribute\Infrastructure\Factory\In_Memory\In_Memory_Attribute_Template_Group_Factory();
        };

        $this->container['affilicious.product.application.setup.product'] = function ($c) {
            return new \Affilicious\Product\Application\Setup\Product_Setup(
                $c['affilicious.shop.infrastructure.repository.shop_template'],
                $c['affilicious.attribute.infrastructure.repository.attribute_template_group'],
                $c['affilicious.detail.infrastructure.repository.detail_template_group']
            );
        };

        $this->container['affilicious.product.application.listener.save_product'] = function ($c) {
            return new \Affilicious\Product\Application\Listener\Save_Product_Listener(
                $c['affilicious.product.infrastructure.repository.product']
            );
        };

        $this->container['affilicious.shop.application.setup.shop_template'] = function ($c) {
            return new \Affilicious\Shop\Application\Setup\Shop_Template_Setup(
                $c['affilicious.shop.infrastructure.repository.provider']
            );
        };

        $this->container['affilicious.detail.application.setup.detail_template_group'] = function () {
            return new \Affilicious\Detail\Application\Setup\Detail_Template_Group_Setup();
        };

        $this->container['affilicious.attribute.application.setup.attribute_template_group'] = function () {
            return new \Affilicious\Attribute\Application\Setup\Attribute_Template_Group_Setup();
        };

	    $this->container['affilicious.common.application.options.affilicious'] = function () {
		    return new \Affilicious\Common\Application\Options\Affilicious_Options();
	    };

	    $this->container['affilicious.product.application.options.product'] = function () {
		    return new \Affilicious\Product\Application\Options\Product_Options();
	    };

        $this->container['affilicious.product.presentation.setup.canonical'] = function () {
            return new \Affilicious\Product\Presentation\Setup\Canonical_Setup();
        };

        $this->container['affilicious.shop.application.options.amazon'] = function ($c) {
            return new \Affilicious\Shop\Application\Options\Amazon_Options(
                $c['affilicious.shop.application.validator.amazon_credentials']
            );
        };

        $this->container['affilicious.product.presentation.setup.admin_bar'] = function () {
            return new \Affilicious\Product\Presentation\Setup\Admin_Bar_Setup();
        };

        $this->container['affilicious.product.presentation.filter.table_content'] = function () {
            return new \Affilicious\Product\Presentation\Filter\Table_Content_Filter();
        };

        $this->container['affilicious.product.presentation.filter.table_count'] = function () {
            return new \Affilicious\Product\Presentation\Filter\Table_Count_Filter();
        };

        $this->container['affilicious.product.presentation.filter.complex_product'] = function () {
            return new \Affilicious\Product\Presentation\Filter\Complex_Product_Filter();
        };

        $this->container['affilicious.product.presentation.setup.slug_rewrite'] = function () {
            return new \Affilicious\Product\Presentation\Setup\Slug_Rewrite_Setup();
        };

        $this->container['affilicious.shop.presentation.setup.table_column'] = function ($c) {
            return new \Affilicious\Shop\Presentation\Setup\Table_Column_Setup(
                $c['affilicious.shop.infrastructure.repository.provider']
            );
        };

        $this->container['affilicious.product.application.update.timer'] = function ($c) {
            return new \Affilicious\Product\Application\Update\Update_Timer(
                $c['affilicious.product.application.update.manager']
            );
        };

        $this->container['affilicious.product.application.update.mediator'] = function () {
            return new \Affilicious\Product\Application\Update\Queue\Update_Mediator();
        };

        $this->container['affilicious.product.application.update.manager'] = function ($c) {
            return new \Affilicious\Product\Application\Update\Manager\Update_Manager(
                $c['affilicious.product.application.update.mediator'],
                $c['affilicious.product.infrastructure.repository.product']
            );
        };

        $this->container['affilicious.product.application.setup.update_worker'] = function ($c) {
            return new \Affilicious\Product\Application\Setup\Update_Worker_Setup(
                $c['affilicious.product.application.update.manager']
            );
        };

        $this->container['affilicious.product.application.setup.amazon_update_worker'] = function () {
            return new \Affilicious\Product\Application\Setup\Amazon_Update_Worker_Setup();
        };

        $this->container['affilicious.product.application.setup.update_mediator'] = function ($c) {
            return new \Affilicious\Product\Application\Setup\Update_Mediator_Setup(
                $c['affilicious.product.application.update.mediator']
            );
        };

        $this->container['affilicious.shop.application.validator.amazon_credentials'] = function() {
            return new \Affilicious\Shop\Application\Validator\Amazon_Credentials_Validator();
        };

        $this->container['affilicious.product.application.migration.post_type'] = function() {
            return new \Affilicious\Product\Application\Migration\Post_Type_Migration();
        };

        $this->container['affilicious.shop.application.migration.post_type'] = function() {
            return new \Affilicious\Shop\Application\Migration\Post_Type_Migration();
        };

        $this->container['affilicious.shop.application.migration.currency_code'] = function() {
            return new \Affilicious\Shop\Application\Migration\Currency_Code_Migration();
        };
    }

    /**
     * Register the plugin textdomain for internationalization.
     *
     * @since 0.5.1
     */
    public function load_textdomain()
    {
        $dir = basename(dirname( __FILE__ ) ) . '/' . self::PLUGIN_LANGUAGE_DIR;
        load_plugin_textdomain(self::PLUGIN_NAME, false, $dir);
    }

	/**
	 * Load the includes
	 *
	 * @since 0.5.1
	 */
    public function load_includes()
    {
	    require_once(self::PLUGIN_SOURCE_DIR . 'common/presentation/form/carbon/hidden-field.php');
	    require_once(self::PLUGIN_SOURCE_DIR . 'common/presentation/form/carbon/number-field.php');

	    if (!class_exists('EDD_SL_Plugin_Updater')) {
		    include(dirname(__FILE__) . '/affilicious-plugin-updater.php');
	    }
    }

	/**
	 * Load the simple functions for an easier usage in templates
	 *
	 * @since 0.5.1
	 */
    public function load_functions()
    {
	    require_once(__DIR__ . '/src/fallbacks.php');
	    require_once(__DIR__ . '/src/functions.php');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     *
     * @since 0.3
     */
    public function register_public_hooks()
    {
    	// Hook the text domain
	    add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Hook the public assets
        $asset_setup = $this->container['affilicious.common.application.setup.asset'];
        add_action('wp_enqueue_scripts', array($asset_setup, 'add_public_styles'), 10);
        add_action('wp_enqueue_scripts', array($asset_setup, 'add_public_scripts'), 20);

        // Hook the Carbon Fields
        $carbon_fields_setup = $this->container['affilicious.common.application.setup.carbon'];
        add_action('after_setup_theme', array($carbon_fields_setup, 'crb_init_carbon_field_hidden'), 15);

        // Hook the providers
        $provider_setup = $this->container['affilicious.shop.application.setup.provider'];
        add_action('init', array($provider_setup, 'init'), 10);

        // Hook the amazon provider
        $amazon_provider_setup = $this->container['affilicious.shop.application.setup.amazon_provider'];
        add_filter('affilicious_shop_provider_setup_init', array($amazon_provider_setup, 'init'));

        // Hook the shops
        $shop_template_setup = $this->container['affilicious.shop.application.setup.shop_template'];
        add_action('init', array($shop_template_setup, 'init'), 20);
        add_action('init', array($shop_template_setup, 'render'), 30);

        // Hook the attribute groups
        $attribute_template_group_setup = $this->container['affilicious.attribute.application.setup.attribute_template_group'];
        add_action('init', array($attribute_template_group_setup, 'init'), 40);
        add_action('init', array($attribute_template_group_setup, 'render'), 50);

        // Hook the detail groups
        $detail_template_group_setup = $this->container['affilicious.detail.application.setup.detail_template_group'];
        add_action('init', array($detail_template_group_setup, 'init'), 60);
        add_action('init', array($detail_template_group_setup, 'render'), 70);

        // Hook the products
        $product_setup = $this->container['affilicious.product.application.setup.product'];
        add_action('init', array($product_setup, 'init'), 80);
        add_action('init', array($product_setup, 'render'), 90);

        // Hook the product listeners
        $save_product_listener = $this->container['affilicious.product.application.listener.save_product'];
        add_action('carbon_after_save_post_meta', array($save_product_listener, 'listen'), 10, 3);

        // Hook the slug rewrite
        $slug_rewrite_setup = $this->container['affilicious.product.presentation.setup.slug_rewrite'];
        add_action('init', array($slug_rewrite_setup, 'run'), 1);
        add_action('added_option', array($slug_rewrite_setup, 'prepare'), 800, 1);
        add_action('updated_option', array($slug_rewrite_setup, 'prepare'), 800, 1);

	    // Hook the settings
        $affilicious_settings = $this->container['affilicious.common.application.options.affilicious'];
        $product_settings = $this->container['affilicious.product.application.options.product'];
	    add_action('init', array($affilicious_settings, 'render'), 10);
	    add_action('init', array($affilicious_settings, 'apply'), 11);
	    add_action('init', array($product_settings, 'render'), 12);
	    add_action('init', array($product_settings, 'apply'), 13);

        // Hook the canonical tags
        $canonical_setup = $this->container['affilicious.product.presentation.setup.canonical'];
        add_action('wp_head', array($canonical_setup, 'set_up'));

        // Hook the admin bar setup
        $admin_bar_setup = $this->container['affilicious.product.presentation.setup.admin_bar'];
        add_action('admin_bar_menu', array($admin_bar_setup, 'set_up'), 999);

        // Filter the complex products from the search
        $complex_product_filter = $this->container['affilicious.product.presentation.filter.complex_product'];
        add_action('pre_get_posts', array($complex_product_filter, 'filter'));

        // Hook the update workers
        $update_worker_setup = $this->container['affilicious.product.application.setup.update_worker'];
        add_action('init', array($update_worker_setup, 'init'), 15);

        // Hook the amazon update worker
        $amazon_update_worker_setup = $this->container['affilicious.product.application.setup.amazon_update_worker'];
        add_filter('affilicious_product_update_worker_setup_init', array($amazon_update_worker_setup, 'init'));

        // Hook the update mediator setup
        $update_mediator_setup = $this->container['affilicious.product.application.setup.update_mediator'];
        add_filter('affilicious_shop_provider_setup_after_init', array($update_mediator_setup, 'init'));

        // Hook the update timer to update the products regularly
        $update_timer = $this->container['affilicious.product.application.update.timer'];
        add_action('affilicious_product_update_run_tasks_hourly', array($update_timer, 'run_tasks_hourly'));
        add_action('affilicious_product_update_run_tasks_twice_daily', array($update_timer, 'run_tasks_twice_daily'));
        add_action('affilicious_product_update_run_tasks_daily', array($update_timer, 'run_tasks_daily'));
    }

    /**
     * Register all of the hooks related to the admin area functionality
     *
     * @since 0.3
     */
    public function register_admin_hooks()
    {
    	// Hook the plugin updater
	    add_action('admin_init', array($this, 'update'), 0);

        // Hook the shops
        $table_columns_filter = $this->container['affilicious.shop.presentation.setup.table_column'];
        add_action('manage_aff_shop_template_posts_columns', array($table_columns_filter, 'columns_head'), 9, 2);
        add_action('manage_aff_shop_template_posts_custom_column', array($table_columns_filter, 'columns_content'), 10, 2);

        $shop_options = $this->container['affilicious.shop.application.options.amazon'];
        add_action('init', array($shop_options, 'render'), 12);

        // Hook the attribute groups
        $attribute_template_group_setup = $this->container['affilicious.attribute.application.setup.attribute_template_group'];
        add_action('manage_aff_attribute_group_posts_columns', array($attribute_template_group_setup, 'columnsHead'), 9, 2);
        add_action('manage_aff_attribute_group_posts_custom_column', array($attribute_template_group_setup, 'columnsContent'), 10, 2);

        // Hook the attribute groups
        $attribute_template_group_setup = $this->container['affilicious.attribute.application.setup.attribute_template_group'];
        add_action('manage_aff_attr_template_posts_columns', array($attribute_template_group_setup, 'columns_head'), 9, 2);
        add_action('manage_aff_attr_template_posts_custom_column', array($attribute_template_group_setup, 'columns_content'), 10, 2);

        // Hook the detail groups
        $detail_template_group_setup = $this->container['affilicious.detail.application.setup.detail_template_group'];
        add_action('manage_detail_group_posts_columns', array($detail_template_group_setup, 'columns_head'), 9, 2);
        add_action('manage_detail_group_posts_custom_column', array($detail_template_group_setup, 'columns_content'), 10, 2);

        // Hook the admin assets
        $asset_setup = $this->container['affilicious.common.application.setup.asset'];
        add_action('admin_enqueue_scripts', array($asset_setup, 'add_admin_styles'), 10);
        add_action('admin_enqueue_scripts', array($asset_setup, 'add_admin_scripts'), 20);

        // Hook the feedback form
        $feedback_setup = $this->container['affilicious.common.application.setup.feedback'];
        add_action('admin_menu', array($feedback_setup, 'init'), 30);

        // Hook the product table setup
        $table_content_filter = $this->container['affilicious.product.presentation.filter.table_content'];
        $table_count_filter = $this->container['affilicious.product.presentation.filter.table_count'];
        add_action('pre_get_posts', array($table_content_filter, 'filter'));
        add_filter("views_edit-aff_product", array($table_count_filter, 'filter'), 10, 1);
    }
}

$affilicious_plugin = Affilicious_Plugin::get_instance();
$affilicious_plugin->run();
