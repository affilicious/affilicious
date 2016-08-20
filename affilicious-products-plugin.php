<?php
/**
 * Plugin Name: Affilicious Produkte
 * Description: Erstelle und verwalte Affilicious Produkte mit den dazugehörigen Feldern und Details in Wordpress
 * Version: 0.3
 * Author: Affilicious Team
 * Author URI: https://affilicioustheme.de/author/alexander-barton
 * Plugin URI: http://affilicioustheme.de/plugins/products
 * License: MIT
 * Requires at least: 4.0
 * Tested up to: 4.6
 * Text Domain: affilicious-products
 * Domain Path: /languages/
 */
use Affilicious\ProductsPlugin\Common\Application\Loader\Loader;
use Affilicious\ProductsPlugin\Common\Application\Setup\AssetSetup;
use Affilicious\ProductsPlugin\Common\Application\Setup\CarbonSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\ProductSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\ShopSetup;
use Affilicious\ProductsPlugin\Product\Application\Setup\DetailGroupSetup;
use Pimple\Container;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonProductRepository;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Wordpress\WordpressShopRepository;
use Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon\CarbonDetailGroupRepository;
use Affilicious\ProductsPlugin\Product\Application\MetaBox\MetaBoxManager;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class AffiliciousProductsPlugin
{
    const PLUGIN_NAME = 'affilicious-products';
    const PLUGIN_VERSION = '0.3';
    const PLUGIN_NAMESPACE = 'Affilicious\\ProductsPlugin\\';
    const PLUGIN_SOURCE_DIR = 'src/';
    const PLUGIN_LANGUAGE_DIR = 'languages';
    const PLUGIN_STORE_URL = 'http://affilicioustheme.de';
    const PLUGIN_ITEM_NAME = 'Affilicious Produkte';
    const PLUGIN_LICENSE_KEY = 'e90a6d1a115da24a292fe0300afc402a';
    const PLUGIN_AUTHOR = 'Affilicious Team';

    /**
     * Register all actions and filters for the plugin.
     *
     * @var Loader
     */
    private static $loader;

    /**
     * Register all services and parameters for the pimple dependency injection
     *
     * @see http://pimple.sensiolabs.org
     * @var Container
     */
    private static $container;

    /**
     * Get a reference to the hooks and filters loader
     *
     * @return Loader
     */
    public static function &getLoader()
    {
        return self::$loader;
    }

    /**
     * Get a reference to the dependency injection container
     *
     * @see https://easydigitaldownloads.com/downloads/software-licensing/
     * @return Container
     */
    public static function &getContainer()
    {
        return self::$container;
    }

    /**
     * Get the root dir of the plugin
     *
     * @return string
     */
    public static function getRootDir()
    {
        return plugin_dir_url( __FILE__ );
    }

    /**
     * Prepare the plugin with for usage with Wordpress and namespaces
     */
    public function __construct()
    {
        require_once(self::PLUGIN_SOURCE_DIR . 'functions.php');
        if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
            require(__DIR__ . '/vendor/autoload.php');
        } else {
            spl_autoload_register(array($this, 'autoload'));
        }
        require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Hidden_Field.php');
        require_once(self::PLUGIN_SOURCE_DIR . 'Common/Application/Form/Carbon/Number_Field.php');

        if (!class_exists('EDD_SL_Plugin_Updater')) {
            include(dirname(__FILE__) . '/affilicious-products-updater.php');
        }

        self::$container = new Container();
        self::$loader = new Loader();

        add_action('admin_init', array($this, 'update'), 0);
    }

    /**
     * Update the plugin with the help of the Software Licensing for Easy Digital Downloads
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
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        register_activation_hook( __FILE__, array($this, 'activate'));
        register_deactivation_hook( __FILE__, array($this, 'deactivate'));
        self::$loader->add_action('plugins_loaded', $this, 'loaded');

        $this->registerServices();
        $this->registerPublicHooks();
        $this->registerAdminHooks();

        new MetaBoxManager(); // This old class will be removed later
        self::$container['carbon_setup'];

        self::$loader->run();
    }

    /**
     * Make namespaces compatible with the source code of this plugin
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
     */
    public function activate()
    {
        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> self::PLUGIN_LICENSE_KEY,
            'item_name' => urlencode(self::PLUGIN_ITEM_NAME), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post(self::PLUGIN_STORE_URL, array(
            'timeout' => 15,
            'sslverify' => false,
            'body' => $api_params
        ));

        // make sure the response came back okay
        return is_wp_error($response);
    }

    /**
     * The code that runs during plugin deactivation.
     */
    public function deactivate()
    {
        // Nothing to do here
    }

    /**
     * The code that runs after the plugin is loaded
     */
    public function loaded()
    {
        $this->registerTextdomain();
    }

    /**
     * Register the services for the dependency injection
     */
    public function registerServices()
    {
        self::$container['product_repository'] = function ($c) {
            return new CarbonProductRepository($c['detail_group_repository']);
        };

        self::$container['shop_repository'] = function () {
            return new WordpressShopRepository();
        };

        self::$container['detail_group_repository'] = function () {
            return new CarbonDetailGroupRepository();
        };

        self::$container['product_setup'] = function ($c) {
            return new ProductSetup($c['detail_group_repository'], $c['shop_repository']);
        };

        self::$container['shop_setup'] = function ($c) {
            return new ShopSetup();
        };

        self::$container['detail_group_setup'] = function () {
            return new DetailGroupSetup();
        };

        self::$container['asset_setup'] = function () {
            return new AssetSetup();
        };

        self::$container['carbon_setup'] = function () {
            return new CarbonSetup();
        };
    }

    /**
     * Register the plugin textdomain for internationalization.
     */
    public function registerTextdomain()
    {
        $dir = basename( dirname( __FILE__ ) ) . '/' . self::PLUGIN_LANGUAGE_DIR;
        load_plugin_textdomain(self::PLUGIN_NAME, false, $dir);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     */
    public function registerPublicHooks()
    {
        // Add public assets
        self::$loader->add_action('wp_enqueue_scripts', self::$container['asset_setup'], 'addPublicStyles', 10);
        self::$loader->add_action('wp_enqueue_scripts', self::$container['asset_setup'], 'addPublicScripts', 20);

        // Set up Carbon Fields
        self::$loader->add_action('after_setup_theme', self::$container['carbon_setup'], 'crb_init_carbon_field_hidden', 15);

        // Set up shops
        self::$loader->add_action('init', self::$container['shop_setup'], 'init', 1);
        self::$loader->add_action('init', self::$container['shop_setup'], 'render', 2);
        self::$loader->add_action('manage_shop_posts_columns', self::$container['shop_setup'], 'columnsHead', 9, 2);
        self::$loader->add_action('manage_shop_posts_custom_column', self::$container['shop_setup'], 'columnsContent', 10, 2);

        // Set up detail groups
        self::$loader->add_action('init', self::$container['detail_group_setup'], 'init', 3);
        self::$loader->add_action('init', self::$container['detail_group_setup'], 'render', 4);

        // Set up products
        self::$loader->add_action('init', self::$container['product_setup'], 'init', 5);
        self::$loader->add_action('init', self::$container['product_setup'], 'render', 6);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    public function registerAdminHooks()
    {
        // Add admin assets
        self::$loader->add_action('admin_enqueue_scripts', self::$container['asset_setup'], 'addAdminStyles', 10);
        self::$loader->add_action('admin_enqueue_scripts', self::$container['asset_setup'], 'addAdminScripts', 20);
    }
}

$affiliciousProductsPlugin = new AffiliciousProductsPlugin();
$affiliciousProductsPlugin->run();
