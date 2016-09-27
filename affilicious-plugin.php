<?php
/**
 * Plugin Name: Affilicious
 * Description: Manage affiliate products in Wordpress with price comparisons, shops, details and more
 * Version: 0.5.2
 * Author: Affilicious Team
 * Author URI: http://affilicioustheme.de/
 * Plugin URI: http://affilicioustheme.de/downloads/affilicious/
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
use Affilicious\Common\Application\Setup\AssetSetup;
use Affilicious\Common\Application\Setup\CarbonSetup;
use Affilicious\Product\Application\Setup\ProductSetup;
use Affilicious\Shop\Application\Setup\ShopSetup;
use Affilicious\Detail\Application\Setup\DetailGroupSetup;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\Shop\Infrastructure\Persistence\Wordpress\WordpressShopRepository;
use Affilicious\Detail\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Affilicious\Product\Infrastructure\Persistence\Carbon\CarbonProductVariantRepository;
use Affilicious\Attribute\Infrastructure\Persistence\Carbon\CarbonAttributeGroupRepository;
use Affilicious\Attribute\Application\Setup\AttributeGroupSetup;
use Affilicious\Product\Application\Setup\ProductVariantSetup;
use Affilicious\Product\Application\MetaBox\MetaBoxManager;
use Affilicious\Common\Application\Setup\FeedbackSetup;
use Affilicious\Settings\Application\Setting\AffiliciousSettings;
use Affilicious\Settings\Application\Setting\ProductSettings;
use Pimple\Container;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AffiliciousPlugin
{
    const PLUGIN_NAME = 'affilicious';
    const PLUGIN_VERSION = '0.5.2';
    const PLUGIN_NAMESPACE = 'Affilicious\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_LANGUAGE_DIR = 'languages/';
    const PLUGIN_STORE_URL = 'http://affilicioustheme.de';
    const PLUGIN_ITEM_NAME = 'Affilicious';
    const PLUGIN_LICENSE_KEY = 'e90a6d1a115da24a292fe0300afc402a';
    const PLUGIN_AUTHOR = 'Affilicious Team';

    /**
     * Stores the singleton instance
     *
     * @since 0.3
     * @var AffiliciousPlugin
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
     * @return AffiliciousPlugin
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AffiliciousPlugin();
        }

        return self::$instance;
    }

    /**
     * Get the root dir of the plugin
     *
     * @since 0.3
     * @return string
     */
    public static function getRootDir()
    {
        return plugin_dir_url( __FILE__ );
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
     * @see https://easydigitaldownloads.com/downloads/software-licensing/
     * @since 0.3
     * @return Container
     */
    public function getContainer()
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

		$this->loadIncludes();
		$this->loadFunctions();
		$this->registerServices();
		$this->registerPublicHooks();
		$this->registerAdminHooks();

		new MetaBoxManager(); // TODO: This old class will be removed later
		// We have to call the container to the run code inside
		$this->container['affilicious.common.setup.carbon'];
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
        $prefix = 'Affilicious';
        if (stripos($class, $prefix) === false) {
            return;
        }
        $file_path = __DIR__ . '/' . self::PLUGIN_SOURCE_DIR . str_ireplace(self::PLUGIN_NAMESPACE, '', $class) . '.php';
        $file_path = str_replace('\\', DIRECTORY_SEPARATOR, $file_path);
        include_once($file_path);
    }

    /**
     * The code that runs during plugin activation.
     *
     * @since 0.3
     */
    public function activate()
    {
        // Data to send in our API request.
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> self::PLUGIN_LICENSE_KEY,
            'item_name' => urlencode(self::PLUGIN_ITEM_NAME),
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // Make sure the response came back okay
        $isError = is_wp_error($response);

	    return $isError;
    }

    /**
     * The code that runs during plugin deactivation.
     *
     * @since 0.3
     */
    public function deactivate()
    {
    	// Data to send in our API request.
	    $api_params = array(
		    'edd_action'=> 'deactivate_license',
		    'license' 	=> self::PLUGIN_LICENSE_KEY,
		    'item_name' => urlencode(self::PLUGIN_ITEM_NAME),
		    'url'       => home_url()
	    );

	    // Call the custom API.
	    $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
	    	'timeout' => 15,
		    'sslverify' => false,
		    'body' => $api_params
	    ));

	    // Make sure the response came back okay
	    $isError = is_wp_error($response);

	    return $isError;
    }

    /**
     * Register the services for the dependency injection
     *
     * @since 0.3
     */
    public function registerServices()
    {
        $this->container['affilicious.common.setup.asset'] = function () {
            return new AssetSetup();
        };

        $this->container['affilicious.common.setup.carbon'] = function () {
            return new CarbonSetup();
        };

        $this->container['affilicious.common.setup.feedback'] = function () {
            return new FeedbackSetup();
        };

        $this->container['affilicious.product.repository.product'] = function ($c) {
            return new CarbonProductRepository(
                $c['affilicious.detail.repository.detail_group'],
                $c['affilicious.shop.repository.shop']
            );
        };

        $this->container['affilicious.product.repository.product_variant'] = function ($c) {
            return new CarbonProductVariantRepository(
                $c['affilicious.detail.repository.detail_group'],
                $c['affilicious.shop.repository.shop']
            );
        };

        $this->container['affilicious.shop.repository.shop'] = function () {
            return new WordpressShopRepository();
        };

        $this->container['affilicious.detail.repository.detail_group'] = function () {
            return new CarbonDetailGroupRepository();
        };

        $this->container['affilicious.attribute.repository.attribute_group'] = function () {
            return new CarbonAttributeGroupRepository();
        };

        $this->container['affilicious.product.setup.product'] = function ($c) {
            return new ProductSetup(
                $c['affilicious.detail.repository.detail_group'],
                $c['affilicious.shop.repository.shop']
            );
        };

        $this->container['affilicious.product.setup.product_variant'] = function ($c) {
            return new ProductVariantSetup();
        };

        $this->container['affilicious.shop.setup.shop'] = function () {
            return new ShopSetup();
        };

        $this->container['affilicious.detail.setup.detail_group'] = function () {
            return new DetailGroupSetup();
        };

        $this->container['affilicious.attribute.setup.attribute_group'] = function () {
            return new AttributeGroupSetup();
        };

	    $this->container['affilicious.settings.setting.affilicious'] = function () {
		    return new AffiliciousSettings();
	    };

	    $this->container['affilicious.settings.setting.product'] = function () {
		    return new ProductSettings();
	    };
    }

    /**
     * Register the plugin textdomain for internationalization.
     *
     * @since 0.5.1
     */
    public function loadTextdomain()
    {
        $dir = basename( dirname( __FILE__ ) ) . '/' . self::PLUGIN_LANGUAGE_DIR;
        load_plugin_textdomain(self::PLUGIN_NAME, false, $dir);
    }

	/**
	 * Load the includes
	 *
	 * @since 0.5.1
	 */
    public function loadIncludes()
    {
	    require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Hidden_Field.php');
	    require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Number_Field.php');

	    if (!class_exists('EDD_SL_Plugin_Updater')) {
		    include(dirname(__FILE__) . '/affilicious-plugin-updater.php');
	    }
    }

	/**
	 * Load the simple functions for an easier usage in templates
	 *
	 * @since 0.5.1
	 */
    public function loadFunctions()
    {
	    require_once(__DIR__ . '/src/functions.php');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     *
     * @since 0.3
     */
    public function registerPublicHooks()
    {
    	// Hook the text domain
	    add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // Hook the public assets
        add_action('wp_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addPublicStyles'), 10);
        add_action('wp_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addPublicScripts'), 20);

        // Hook the Carbon Fields
        add_action('after_setup_theme', array($this->container['affilicious.common.setup.carbon'], 'crb_init_carbon_field_hidden'), 15);

        // Hook the shops
        add_action('init', array($this->container['affilicious.shop.setup.shop'], 'init'), 1);
        add_action('init', array($this->container['affilicious.shop.setup.shop'], 'render'), 2);
        add_action('manage_shop_posts_columns', array($this->container['affilicious.shop.setup.shop'], 'columnsHead'), 9, 2);
        add_action('manage_shop_posts_custom_column', array($this->container['affilicious.shop.setup.shop'], 'columnsContent'), 10, 2);

        // Hook the attribute groups
        add_action('init', array($this->container['affilicious.attribute.setup.attribute_group'], 'init'), 3);
        add_action('init', array($this->container['affilicious.attribute.setup.attribute_group'], 'render'), 4);

        // Hook the detail groups
        add_action('init', array($this->container['affilicious.detail.setup.detail_group'], 'init'), 3);
        add_action('init', array($this->container['affilicious.detail.setup.detail_group'], 'render'), 4);

        // Hook the products
        add_action('init', array($this->container['affilicious.product.setup.product'], 'init'), 5);
        add_action('init', array($this->container['affilicious.product.setup.product'], 'render'), 6);

        // Hook the product variants
        add_action('init', array($this->container['affilicious.product.setup.product_variant'], 'init'), 5);
        add_action('init', array($this->container['affilicious.product.setup.product_variant'], 'render'), 6);

	    // Hook the settings
	    add_action('init', array($this->container['affilicious.settings.setting.affilicious'], 'render'), 10);
	    add_action('init', array($this->container['affilicious.settings.setting.affilicious'], 'apply'), 11);
	    add_action('init', array($this->container['affilicious.settings.setting.product'], 'render'), 12);
	    add_action('init', array($this->container['affilicious.settings.setting.product'], 'apply'), 13);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     *
     * @since 0.3
     */
    public function registerAdminHooks()
    {
    	// Hook the plugin updater
	    add_action('admin_init', array($this, 'update'), 0);

        // Hook the admin assets
        add_action('admin_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addAdminStyles'), 10);
        add_action('admin_enqueue_scripts', array($this->container['affilicious.common.setup.asset'], 'addAdminScripts'), 20);

        // Hook the feedback form
        add_action('admin_menu', array($this->container['affilicious.common.setup.feedback'], 'init'), 30);
    }
}

$affiliciousPlugin = AffiliciousPlugin::getInstance();
$affiliciousPlugin->run();
